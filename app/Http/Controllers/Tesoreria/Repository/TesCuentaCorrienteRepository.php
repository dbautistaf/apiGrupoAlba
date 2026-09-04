<?php

namespace App\Http\Controllers\Tesoreria\Repository;

use Illuminate\Support\Facades\DB;

/**
 * Cuenta corriente de prestadores y proveedores (punto 10).
 *
 * Responde "¿cuánto le debemos a este prestador y por qué?": las facturas que generaron deuda, lo
 * que se le fue pagando, los anticipos con saldo a favor, y el neto.
 *
 * Se apoya entera en el FIFO y en los anticipos: no guarda ningún saldo propio. Un saldo
 * almacenado se desfasa el día que alguien anula un pago por otro camino.
 *
 * ═══ Qué cuenta como deuda ═══
 *
 * NO toda factura cargada es deuda exigible. El criterio es:
 *
 *   - facturas en VALORIZACIÓN FINAL (estado 3), que es el punto donde Liquidaciones las da por
 *     buenas y quedan listas para generar la orden de pago; MÁS
 *   - cualquier factura ya imputada en una OP viva, sin importar su estado — si alguien la puso
 *     en una orden, ya la trató como pagable.
 *
 * Quedan afuera las ANULADAS (estado 4) siempre.
 *
 * Sin este filtro la deuda se inflaría con las facturas en estado 0 (abiertas), que en estas
 * bases suman más de $543M y todavía no están aprobadas por nadie.
 */
class TesCuentaCorrienteRepository
{
    const ESTADO_FACTURA_VALORIZACION_FINAL = 3;
    const ESTADO_FACTURA_ANULADA            = 4;

    private $fifo;
    private $anticipos;

    public function __construct(TesImputacionFifoRepository $fifo, TesAnticipoRepository $anticipos)
    {
        $this->fifo      = $fifo;
        $this->anticipos = $anticipos;
    }

    private static function aCentavos($monto): int
    {
        return (int) round(((float) $monto) * 100);
    }

    private static function aPesos(int $centavos): float
    {
        return round($centavos / 100, 2);
    }

    private static function campoBeneficiario(string $tipo): string
    {
        return strtoupper(trim($tipo)) === 'PROVEEDOR' ? 'id_proveedor' : 'id_prestador';
    }

    /** Facturas que representan deuda de este beneficiario. Ver el criterio en la cabecera. */
    public function facturasConDeuda($idBeneficiario, string $tipo, ?string $desde = null, ?string $hasta = null)
    {
        $campo = self::campoBeneficiario($tipo);

        return DB::table('tb_facturacion_datos as f')
            ->where("f.{$campo}", $idBeneficiario)
            ->where('f.estado', '!=', self::ESTADO_FACTURA_ANULADA)
            ->where(function ($q) {
                $q->where('f.estado', self::ESTADO_FACTURA_VALORIZACION_FINAL)
                    ->orWhereExists(function ($sub) {
                        $sub->select(DB::raw(1))
                            ->from('tb_tes_opa_factura as pf')
                            ->join('tb_tes_orden_pago as o', 'o.id_orden_pago', '=', 'pf.id_orden_pago')
                            ->whereColumn('pf.id_factura', 'f.id_factura')
                            ->where('o.id_estado_orden_pago', '!=', TestOrdenPagoRepository::ESTADO_OPA_RECHAZADO);
                    });
            })
            ->when($desde, fn($q) => $q->whereDate('f.fecha_comprobante', '>=', $desde))
            ->when($hasta, fn($q) => $q->whereDate('f.fecha_comprobante', '<=', $hasta))
            ->select([
                'f.id_factura', 'f.numero', 'f.periodo', 'f.fecha_comprobante', 'f.estado',
                'f.total_neto', 'f.total_debitado_liquidacion',
            ])
            ->orderBy('f.fecha_comprobante')
            ->orderBy('f.id_factura')
            ->get();
    }

    /**
     * Resumen de la cuenta corriente.
     *
     * `saldo_neto` es lo que realmente habría que pagarle hoy: la deuda pendiente menos el saldo
     * a favor que ya se le adelantó. Puede dar negativo — significa que se le pagó de más.
     */
    public function resumen($idBeneficiario, string $tipo, ?string $desde = null, ?string $hasta = null): array
    {
        $facturas = $this->facturasConDeuda($idBeneficiario, $tipo, $desde, $hasta);

        $facturado = 0;
        $pagado    = 0;

        foreach ($facturas as $f) {
            $aPagar = self::aCentavos($f->total_neto) - self::aCentavos($f->total_debitado_liquidacion ?? 0);
            $facturado += $aPagar;
            $pagado += self::aCentavos($this->fifo->pagadoDeFactura($f->id_factura));
        }

        $anticipos = self::aCentavos($this->anticipos->saldoAFavor($idBeneficiario, $tipo));
        $pendiente = $facturado - $pagado;

        return [
            'id_beneficiario'       => $idBeneficiario,
            'tipo_beneficiario'     => strtoupper(trim($tipo)),
            'cantidad_facturas'     => $facturas->count(),
            'total_facturado'       => self::aPesos($facturado),
            'total_pagado'          => self::aPesos($pagado),
            'deuda_pendiente'       => self::aPesos(max(0, $pendiente)),
            'anticipos_disponibles' => self::aPesos($anticipos),
            'saldo_neto'            => self::aPesos($pendiente - $anticipos),
        ];
    }

    /**
     * Movimientos en orden cronológico, con saldo acumulado.
     *
     * Cada factura suma al debe; cada peso efectivamente cobrado suma al haber. Los anticipos
     * van aparte: no cancelan una factura hasta que se aplican, así que mostrarlos mezclados
     * haría parecer saldada una deuda que sigue viva.
     */
    public function movimientos($idBeneficiario, string $tipo, ?string $desde = null, ?string $hasta = null): array
    {
        $facturas = $this->facturasConDeuda($idBeneficiario, $tipo, $desde, $hasta);
        $movimientos = [];

        foreach ($facturas as $f) {
            $aPagar = self::aCentavos($f->total_neto) - self::aCentavos($f->total_debitado_liquidacion ?? 0);
            $cobrado = self::aCentavos($this->fifo->pagadoDeFactura($f->id_factura));

            $movimientos[] = [
                'fecha'       => $f->fecha_comprobante,
                'tipo'        => 'FACTURA',
                'comprobante' => $f->numero,
                'periodo'     => $f->periodo,
                'detalle'     => 'Factura ' . $f->numero,
                'debe'        => self::aPesos($aPagar),
                'haber'       => 0.0,
                'id_factura'  => $f->id_factura,
            ];

            if ($cobrado > 0) {
                $movimientos[] = [
                    'fecha'       => $f->fecha_comprobante,
                    'tipo'        => 'COBRO',
                    'comprobante' => $f->numero,
                    'periodo'     => $f->periodo,
                    'detalle'     => 'Pagos imputados a la factura ' . $f->numero,
                    'debe'        => 0.0,
                    'haber'       => self::aPesos($cobrado),
                    'id_factura'  => $f->id_factura,
                ];
            }
        }

        // Orden cronológico estable: ante misma fecha, primero la factura y después su cobro.
        usort($movimientos, function ($a, $b) {
            if ($a['fecha'] === $b['fecha']) {
                if ($a['id_factura'] === $b['id_factura']) {
                    return $a['tipo'] === 'FACTURA' ? -1 : 1;
                }

                return $a['id_factura'] <=> $b['id_factura'];
            }

            return strcmp((string) $a['fecha'], (string) $b['fecha']);
        });

        $acumulado = 0;

        foreach ($movimientos as &$m) {
            $acumulado += self::aCentavos($m['debe']) - self::aCentavos($m['haber']);
            $m['saldo'] = self::aPesos($acumulado);
        }

        return $movimientos;
    }

    /** Cuenta corriente completa: resumen, movimientos y anticipos con saldo. */
    public function cuentaCorriente($idBeneficiario, string $tipo, ?string $desde = null, ?string $hasta = null): array
    {
        return [
            'resumen'     => $this->resumen($idBeneficiario, $tipo, $desde, $hasta),
            'movimientos' => $this->movimientos($idBeneficiario, $tipo, $desde, $hasta),
            'anticipos'   => $this->anticipos->anticiposConSaldo($idBeneficiario, $tipo),
        ];
    }
}
