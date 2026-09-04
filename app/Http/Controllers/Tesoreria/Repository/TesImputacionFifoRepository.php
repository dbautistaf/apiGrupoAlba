<?php

namespace App\Http\Controllers\Tesoreria\Repository;

use Illuminate\Support\Facades\DB;

/**
 * Imputación FIFO: decide qué facturas quedaron cubiertas por lo que efectivamente se pagó.
 *
 * La regla es "primero la más vieja, y se cancela entera antes de pasar a la siguiente".
 * NO se reparte proporcionalmente: si se repartiera, todas las facturas quedarían un poco
 * pagadas y ninguna cerrada, que es justo lo que no sirve para conciliar con el prestador.
 *
 * ═══ El resultado se CALCULA, no se guarda ═══
 *
 * No hay una columna con "lo pagado de esta factura". La distribución es una función pura de
 * (facturas ordenadas, total efectivamente pagado), y las dos cosas ya viven en la base:
 * `tb_tes_opa_factura.monto_aplicado` y los pagos. Guardar el resultado agregaría un dato que
 * puede quedar desfasado del que lo origina — que es exactamente el problema que tuvimos con el
 * monto de cabecera contra el detalle.
 *
 * ═══ El orden tiene que ser determinístico ═══
 *
 * `fecha_comprobante` sola NO alcanza: en estas bases hay hasta 6 facturas de la misma OP con la
 * misma fecha (verificado 2026-09-03). Sin un desempate fijo, dos corridas del mismo pago podrían
 * cancelar facturas distintas, y el usuario vería "ayer decía que estaba paga la 7157 y hoy dice
 * la 7160". Se desempata por `id_factura`, que es inmutable.
 *
 * ═══ Se trabaja en centavos ═══
 *
 * Toda la aritmética va en enteros. Con float, restar importes en cadena deja restos de
 * 0.00000001 que hacen que la última factura quede "casi" cubierta y nunca cierre.
 */
class TesImputacionFifoRepository
{
    const CUBIERTA  = 'CUBIERTA';
    const PARCIAL   = 'PARCIAL';
    const PENDIENTE = 'PENDIENTE';

    private $opaRepository;

    public function __construct(TestOrdenPagoRepository $opaRepository)
    {
        $this->opaRepository = $opaRepository;
    }

    private static function aCentavos($monto): int
    {
        return (int) round(((float) $monto) * 100);
    }

    private static function aPesos(int $centavos): float
    {
        return round($centavos / 100, 2);
    }

    /**
     * Facturas de una OP en el orden en que se van a ir cancelando.
     *
     * Sale de la tabla puente, que es la fuente de la imputación.
     */
    public function facturasOrdenadas($idOpa)
    {
        return DB::table('tb_tes_opa_factura as pf')
            ->join('tb_facturacion_datos as f', 'f.id_factura', '=', 'pf.id_factura')
            ->where('pf.id_orden_pago', $idOpa)
            ->select([
                'pf.id_factura',
                'pf.monto_aplicado',
                'f.numero',
                'f.periodo',
                'f.fecha_comprobante',
            ])
            ->orderBy('f.fecha_comprobante')
            ->orderBy('pf.id_factura')
            ->get();
    }

    /**
     * Distribuye lo pagado de una OP entre sus facturas, de la más vieja a la más nueva.
     *
     * @return array{
     *   id_orden_pago: mixed, total_imputado: float, total_pagado: float, sin_aplicar: float,
     *   facturas: array<int, array{id_factura: mixed, numero: mixed, periodo: mixed,
     *     fecha_comprobante: mixed, imputado: float, pagado: float, saldo: float, estado: string}>
     * }
     */
    public function distribuir($idOpa): array
    {
        $facturas = $this->facturasOrdenadas($idOpa);

        // Lo que realmente se pagó, con el mismo criterio que usa el estado derivado de la OP:
        // sólo pagos confirmados/acreditados, nunca los rechazados.
        $restante = self::aCentavos($this->opaRepository->montoPagadoOpa($idOpa));

        $totalImputado = 0;
        $detalle = [];

        foreach ($facturas as $f) {
            $imputado = self::aCentavos($f->monto_aplicado);
            $totalImputado += $imputado;

            // Se cancela entera antes de pasar a la siguiente: acá está el FIFO.
            $aplicado = max(0, min($restante, $imputado));
            $restante -= $aplicado;

            if ($aplicado >= $imputado && $imputado > 0) {
                $estado = self::CUBIERTA;
            } elseif ($aplicado > 0) {
                $estado = self::PARCIAL;
            } else {
                $estado = self::PENDIENTE;
            }

            $detalle[] = [
                'id_factura'        => $f->id_factura,
                'numero'            => $f->numero,
                'periodo'           => $f->periodo,
                'fecha_comprobante' => $f->fecha_comprobante,
                'imputado'          => self::aPesos($imputado),
                'pagado'            => self::aPesos($aplicado),
                'saldo'             => self::aPesos($imputado - $aplicado),
                'estado'            => $estado,
            ];
        }

        return [
            'id_orden_pago'  => $idOpa,
            'total_imputado' => self::aPesos($totalImputado),
            'total_pagado'   => self::aPesos($this->montoPagadoEnCentavos($idOpa)),
            // Si sobra plata después de cubrir todas las facturas, es un pago de más o un
            // anticipo. No se lo come el cálculo: se informa para que se vea.
            'sin_aplicar'    => self::aPesos(max(0, $restante)),
            'facturas'       => $detalle,
        ];
    }

    private function montoPagadoEnCentavos($idOpa): int
    {
        return self::aCentavos($this->opaRepository->montoPagadoOpa($idOpa));
    }

    /**
     * Cuánto se pagó de UNA factura, sumando todas las OPs vivas que la imputan.
     *
     * Una factura puede aparecer en más de una OP (por ejemplo si una vieja quedó anulada y otra
     * la reemplaza). Las anuladas no cuentan: su imputación es historia, no deuda cancelada.
     *
     * Es la base del estado de la factura (punto 3) y de la cuenta corriente (punto 10).
     */
    public function pagadoDeFactura($idFactura): float
    {
        $opas = DB::table('tb_tes_opa_factura as pf')
            ->join('tb_tes_orden_pago as o', 'o.id_orden_pago', '=', 'pf.id_orden_pago')
            ->where('pf.id_factura', $idFactura)
            ->where('o.id_estado_orden_pago', '!=', TestOrdenPagoRepository::ESTADO_OPA_RECHAZADO)
            ->pluck('pf.id_orden_pago');

        $total = 0;

        foreach ($opas as $idOpa) {
            foreach ($this->distribuir($idOpa)['facturas'] as $f) {
                if ((int) $f['id_factura'] === (int) $idFactura) {
                    $total += self::aCentavos($f['pagado']);
                }
            }
        }

        return self::aPesos($total);
    }

    /**
     * Estado de pago de una factura según lo efectivamente cobrado.
     *
     * Se compara contra el neto menos los débitos de liquidación, que es lo que realmente se le
     * debe al prestador — no contra el bruto.
     */
    public function estadoDeFactura($idFactura): array
    {
        $f = DB::table('tb_facturacion_datos')
            ->where('id_factura', $idFactura)
            ->select(['id_factura', 'numero', 'total_neto', 'total_debitado_liquidacion'])
            ->first();

        if (is_null($f)) {
            return ['id_factura' => $idFactura, 'estado' => self::PENDIENTE,
                    'a_pagar' => 0.0, 'pagado' => 0.0, 'saldo' => 0.0];
        }

        $aPagar = self::aCentavos($f->total_neto) - self::aCentavos($f->total_debitado_liquidacion ?? 0);
        $pagado = self::aCentavos($this->pagadoDeFactura($idFactura));

        if ($aPagar > 0 && $pagado >= $aPagar) {
            $estado = self::CUBIERTA;
        } elseif ($pagado > 0) {
            $estado = self::PARCIAL;
        } else {
            $estado = self::PENDIENTE;
        }

        return [
            'id_factura' => $f->id_factura,
            'numero'     => $f->numero,
            'estado'     => $estado,
            'a_pagar'    => self::aPesos($aPagar),
            'pagado'     => self::aPesos($pagado),
            'saldo'      => self::aPesos(max(0, $aPagar - $pagado)),
        ];
    }
}
