<?php

namespace App\Http\Controllers\Tesoreria\Repository;

use App\Models\Tesoreria\TesCuentasBancariasEntity;
use App\Models\Tesoreria\TesOrdenPagoEntity;
use App\Models\Tesoreria\TesPagoEntity;
use App\Models\Tesoreria\TesPagosParciales;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

/**
 * Ciclo de vida del instrumento de pago (eCheq).
 *
 * ═══ Dónde vive el instrumento ═══
 *
 *     Orden de pago (tb_tes_orden_pago)
 *      └── 1 boleta de pago (tb_tes_pago)
 *           ├── N fechas probables (tb_tes_fecha_probable_pago)  — el cronograma
 *           └── N abonos          (tb_tes_pago_parcial)          — CADA eCHEQ ES UNO DE ESTOS
 *
 * **Cada eCheq es un ABONO**, no una boleta. Verificado contra los datos el 2026-09-04: ninguna
 * de las 4.187 OPAs tiene más de una boleta, 23 boletas tienen más de un abono, y 81 números de
 * cheque viven en `tb_tes_pago_parcial` contra 9 en `tb_tes_pago`.
 *
 * La primera versión de este repositorio operaba sobre la boleta. Estaba un nivel demasiado
 * arriba: dos eCheq de una misma orden habrían exigido dos boletas, algo que no ocurre en ningún
 * caso real y que la guarda de `getCrearPago` impide. Corregido en 2026_09_04_100900.
 *
 * ═══ MÁQUINA DE ESTADOS ═══
 *
 *   BORRADOR (1)          Pagos definió el abono (monto y fecha). La OP todavía no se imprimió.
 *        |
 *        v  marcarPendienteEmision()  — se imprime la OP inicial, SIN números
 *   PENDIENTE_EMISION (2) Esperando que el banco emita. El número se carga acá como borrador.
 *        |
 *        v  confirmarEmisionDeOpa()   — se confirman TODOS los números de la OP juntos
 *   EMITIDO (3)
 *        +--> ACREDITADO (4)  la conciliación bancaria confirmó el débito
 *        +--> RECHAZADO (5)   volvió — LO CARGA EL USUARIO A MANO (negocio, 2026-09-03)
 *        +--> ANULADO (6)
 *
 * El número de eCheq es único en toda la tabla: un eCheq cubre un solo abono. Lo garantiza el
 * índice `uq_pp_numero_echeq`; la validación aplicativa solo da el aviso temprano.
 */
class TesInstrumentoPagoRepository
{
    const BORRADOR          = 1;
    const PENDIENTE_EMISION = 2;
    const EMITIDO           = 3;
    const ACREDITADO        = 4;
    const RECHAZADO         = 5;
    const ANULADO           = 6;

    /**
     * `tb_tes_formas_pago`: 7 = eCheq. Verificado en las DOS bases el 2026-09-03 — los catálogos
     * de formas de pago y de estado de instrumento coinciden id por id entre Alba y OSV.
     */
    const FORMA_PAGO_ECHEQ = 7;

    private $user;
    private $fechaActual;

    public function __construct()
    {
        $this->user = Auth::user();
        $this->fechaActual = Carbon::now('America/Argentina/Buenos_Aires');
    }

    private static function aCentavos($monto): int
    {
        return (int) round(((float) $monto) * 100);
    }

    /** Las boletas de pago vivas de una OP. Los abonos cuelgan de ellas. */
    private function boletasDeOpa($idOpa): array
    {
        return TesPagoEntity::where('id_orden_pago', $idOpa)
            ->where('id_estado_orden_pago', '!=', TestOrdenPagoRepository::ESTADO_OPA_RECHAZADO)
            ->pluck('id_pago')
            ->all();
    }

    /** Abonos de una OP, en cualquier estado. */
    public function abonosDeOpa($idOpa)
    {
        return TesPagosParciales::whereIn('id_pago', $this->boletasDeOpa($idOpa))->get();
    }

    /**
     * Resuelve el banco emisor del eCheq.
     *
     * Si vino la cuenta bancaria, el banco lo determina la cuenta: es la única fuente que no
     * puede contradecirse a sí misma. Si además mandaron un banco explícito y NO coincide, se
     * corta — elegir uno en silencio dejaría el listado por banco mal agrupado sin que nadie
     * se entere.
     *
     * El banco explícito sin cuenta es válido: el catálogo de cuentas está incompleto, así que
     * hay eCheq emitidos desde bancos todavía sin cuenta cargada.
     */
    private function resolverBancoEmisor($idCuenta, $idBancoExplicito): ?int
    {
        if (is_null($idCuenta)) {
            return is_null($idBancoExplicito) ? null : (int) $idBancoExplicito;
        }

        $cuenta = TesCuentasBancariasEntity::find($idCuenta);

        if (is_null($cuenta)) {
            throw new \Exception("No se encontró la cuenta bancaria {$idCuenta}.");
        }

        $idBancoCuenta = (int) $cuenta->id_entidad_bancaria;

        if (!is_null($idBancoExplicito) && (int) $idBancoExplicito !== $idBancoCuenta) {
            throw new \Exception(
                "El banco emisor indicado no coincide con el de la cuenta bancaria {$idCuenta}."
            );
        }

        return $idBancoCuenta;
    }

    /**
     * Fechas planificadas de una OP que todavía NO tienen su pago emitido.
     *
     * Confirmar la orden deja el cronograma; esto devuelve lo que falta emitir de ese plan.
     */
    public function fechasPendientesDeEmitir($idOpa)
    {
        return DB::table('tb_tes_fecha_probable_pago as fp')
            ->whereIn('fp.id_pago', $this->boletasDeOpa($idOpa))
            ->whereNotExists(function ($q) {
                $q->select(DB::raw(1))
                    ->from('tb_tes_pago_parcial as pp')
                    ->whereColumn('pp.id_fecha_probable', 'fp.id_fecha_probable');
            })
            ->orderBy('fp.orden_cuotas')
            ->get();
    }

    /**
     * Emite UN pago sobre una fecha planificada: acá se definen el monto y la forma de pago.
     *
     * Es el momento en que el plan se vuelve un instrumento concreto. Antes de esto solo existe
     * la fecha; el abono no puede existir sin monto ni forma (las dos columnas son NOT NULL, y
     * rellenarlas con ceros sería inventar datos).
     *
     * @param array $datos ['monto' => float, 'id_forma_pago' => int,
     *                      'id_cuenta_bancaria' => ?int, 'id_banco_emisor' => ?int]
     */
    public function emitirPagoDeFecha($idFechaProbable, array $datos): TesPagosParciales
    {
        return DB::transaction(function () use ($idFechaProbable, $datos) {
            $fecha = DB::table('tb_tes_fecha_probable_pago')->where('id_fecha_probable', $idFechaProbable)->first();

            if (is_null($fecha)) {
                throw new \Exception("No se encontró la fecha de pago {$idFechaProbable}.");
            }

            if (TesPagosParciales::where('id_fecha_probable', $idFechaProbable)->exists()) {
                throw new \Exception('Esa fecha de pago ya tiene su pago emitido.');
            }

            $monto = (float) ($datos['monto'] ?? 0);

            if (self::aCentavos($monto) <= 0) {
                throw new \Exception('Hay que indicar el monto del pago.');
            }

            if (empty($datos['id_forma_pago'])) {
                throw new \Exception('Hay que indicar la forma de pago.');
            }

            $boleta = TesPagoEntity::find($fecha->id_pago);
            $opa = TesOrdenPagoEntity::find($boleta->id_orden_pago);

            // Lo ya emitido sobre esta orden, para saber cuánto queda.
            $yaEmitido = (float) TesPagosParciales::whereIn(
                'id_pago',
                TesPagoEntity::where('id_orden_pago', $opa->id_orden_pago)->pluck('id_pago')
            )->sum('monto_pago');

            $esEcheq = (int) $datos['id_forma_pago'] === self::FORMA_PAGO_ECHEQ;

            return TesPagosParciales::create([
                'id_pago'               => $fecha->id_pago,
                'id_fecha_probable'     => $idFechaProbable,
                'fecha_registra'        => $this->fechaActual->toDateString(),
                // No nace confirmado: se confirma cuando el banco acredita.
                'fecha_confirma_pago'   => null,
                'id_forma_pago'         => $datos['id_forma_pago'],
                'monto_pago'            => $monto,
                'monto_opa'             => $opa->monto_orden_pago,
                'monto_restante'        => max(0, (float) $opa->monto_orden_pago - ($yaEmitido + $monto)),
                'id_usuario'            => $this->user->cod_usuario ?? null,
                // El eCheq espera que el banco le asigne número; una transferencia no.
                'id_estado_instrumento' => $esEcheq ? self::PENDIENTE_EMISION : self::EMITIDO,
                'fecha_emision_echeq'   => $fecha->fecha_probable_pago,
                'id_banco_emisor'       => $this->resolverBancoEmisor(
                    $datos['id_cuenta_bancaria'] ?? $boleta->id_cuenta_bancaria ?? null,
                    $datos['id_banco_emisor'] ?? null
                ),
            ]);
        });
    }

    /**
     * ¿El número de eCheq está libre? La unicidad es GLOBAL: un eCheq cubre un solo abono.
     *
     * `$idAbonoExcluir` permite revalidar el propio número al editarlo sin chocar consigo mismo.
     */
    public function numeroEcheqDisponible(?string $numero, $idAbonoExcluir = null): bool
    {
        $numero = trim((string) $numero);

        if ($numero === '') {
            return false;
        }

        return !TesPagosParciales::where('numero_echeq', $numero)
            ->when(!is_null($idAbonoExcluir), fn($q) => $q->where('id_pago_parcial', '!=', $idAbonoExcluir))
            ->exists();
    }

    /**
     * Guarda el número de un eCheq como BORRADOR: queda persistido pero el abono no avanza de
     * estado hasta que se confirme la OP completa.
     *
     * Se guarda con trim: en la columna vieja `num_cheque` hay valores con espacios al borde y
     * no queremos arrastrar eso.
     */
    public function guardarBorradorNumero($idAbono, ?string $numero): TesPagosParciales
    {
        $abono = TesPagosParciales::find($idAbono);

        if (is_null($abono)) {
            throw new \Exception("No se encontró el abono {$idAbono}.");
        }

        if (!in_array((int) $abono->id_estado_instrumento, [self::BORRADOR, self::PENDIENTE_EMISION], true)) {
            throw new \Exception(
                'Solo se puede cargar el número de un pago que todavía no fue emitido.'
            );
        }

        $numero = trim((string) $numero);

        if ($numero !== '' && !$this->numeroEcheqDisponible($numero, $idAbono)) {
            throw new \Exception("El número de eCheq {$numero} ya está usado en otro pago.");
        }

        $abono->numero_echeq = $numero === '' ? null : $numero;
        // `num_cheque` se mantiene en sincronía: es la columna que leen las pantallas viejas
        // y el comprobante de pago que ya existe.
        $abono->num_cheque = $abono->numero_echeq;
        $abono->save();

        return $abono;
    }

    /**
     * Cambia la forma de pago de UN instrumento.
     *
     * Se elige por instrumento y no al confirmar la orden porque una misma OP puede pagarse con
     * un eCheq y una transferencia a la vez (requerimiento, punto 2).
     *
     * Al pasar de eCheq a otra forma se borra el número: un pago que no es eCheq no lo tiene, y
     * dejarlo cargado bloquearía ese número para otro eCheq que sí lo necesite.
     */
    public function cambiarFormaPago($idAbono, $idFormaPago): TesPagosParciales
    {
        $abono = TesPagosParciales::find($idAbono);

        if (is_null($abono)) {
            throw new \Exception("No se encontró el abono {$idAbono}.");
        }

        if (!in_array((int) $abono->id_estado_instrumento, [self::BORRADOR, self::PENDIENTE_EMISION], true)) {
            throw new \Exception('Solo se puede cambiar la forma de pago de un pago que todavía no fue emitido.');
        }

        $abono->id_forma_pago = $idFormaPago;

        if ((int) $idFormaPago !== self::FORMA_PAGO_ECHEQ) {
            $abono->numero_echeq = null;
            $abono->num_cheque = null;
        }

        $abono->save();

        return $abono;
    }

    /**
     * Confirma la emisión de TODOS los eCheq de una OP, en un solo acto.
     *
     * Exige que ninguno haya quedado sin número: el requerimiento pide que se carguen todos
     * juntos y se confirmen de una.
     */
    public function confirmarEmisionDeOpa($idOpa, TestOrdenPagoRepository $opaRepo): int
    {
        return DB::transaction(function () use ($idOpa, $opaRepo) {
            $pendientes = TesPagosParciales::whereIn('id_pago', $this->boletasDeOpa($idOpa))
                ->whereIn('id_estado_instrumento', [self::BORRADOR, self::PENDIENTE_EMISION])
                ->get();

            if ($pendientes->isEmpty()) {
                throw new \Exception('Esta orden de pago no tiene eCheq pendientes de emisión.');
            }

            // Solo el eCheq necesita numero: una transferencia se emite y listo. Por eso la
            // exigencia se aplica unicamente a los eCheq de la orden.
            $sinNumero = $pendientes
                ->filter(fn($p) => (int) $p->id_forma_pago === self::FORMA_PAGO_ECHEQ)
                ->filter(fn($p) => empty(trim((string) $p->numero_echeq)));

            if ($sinNumero->isNotEmpty()) {
                throw new \Exception(
                    'Faltan cargar ' . $sinNumero->count() . ' número(s) de eCheq. '
                    . 'Se confirman todos juntos.'
                );
            }

            foreach ($pendientes as $p) {
                $p->id_estado_instrumento = self::EMITIDO;
                $p->save();
            }

            // Emitir todavía no acredita, así que esto normalmente deja la OP igual. Se recalcula
            // para no depender de supuestos.
            $opaRepo->recalcularEstadoOpa($idOpa);

            return $pendientes->count();
        });
    }

    /**
     * Marca un eCheq como ACREDITADO. Es el único evento del ciclo que llega desde la
     * conciliación bancaria.
     */
    public function marcarAcreditado($idAbono, $fechaAcreditacion, TestOrdenPagoRepository $opaRepo): TesPagosParciales
    {
        return DB::transaction(function () use ($idAbono, $fechaAcreditacion, $opaRepo) {
            $abono = TesPagosParciales::find($idAbono);

            if (is_null($abono)) {
                throw new \Exception("No se encontró el abono {$idAbono}.");
            }

            if ((int) $abono->id_estado_instrumento !== self::EMITIDO) {
                throw new \Exception('Solo se puede acreditar un eCheq que esté emitido.');
            }

            $abono->id_estado_instrumento = self::ACREDITADO;
            $abono->fecha_confirma_pago   = $fechaAcreditacion;
            $abono->save();

            $opaRepo->recalcularEstadoOpa($this->opaDeAbono($abono));

            return $abono;
        });
    }

    /**
     * Marca un eCheq como RECHAZADO. **Carga manual del usuario** — confirmado por negocio el
     * 2026-09-03: el rechazo no llega desde la conciliación bancaria.
     *
     * Al perder la fecha de confirmación deja de contar como cobrado, así que la OP vuelve sola
     * al estado que le corresponde.
     */
    public function marcarRechazado($idAbono, ?string $motivo, TestOrdenPagoRepository $opaRepo): TesPagosParciales
    {
        return DB::transaction(function () use ($idAbono, $motivo, $opaRepo) {
            $abono = TesPagosParciales::find($idAbono);

            if (is_null($abono)) {
                throw new \Exception("No se encontró el abono {$idAbono}.");
            }

            if (!in_array((int) $abono->id_estado_instrumento, [self::EMITIDO, self::ACREDITADO], true)) {
                throw new \Exception('Solo se puede rechazar un eCheq que haya sido emitido.');
            }

            $abono->id_estado_instrumento = self::RECHAZADO;
            $abono->motivo_rechazo        = $motivo;
            $abono->fecha_rechazo         = $this->fechaActual;
            $abono->fecha_confirma_pago   = null;
            $abono->save();

            $opaRepo->recalcularEstadoOpa($this->opaDeAbono($abono));

            return $abono;
        });
    }

    private function opaDeAbono(TesPagosParciales $abono)
    {
        return TesPagoEntity::where('id_pago', $abono->id_pago)->value('id_orden_pago');
    }

    /**
     * Pagos planificados que todavía no se emitieron, más los eCheq emitidos que esperan número.
     *
     * Son las dos cosas que Tesorería tiene pendientes de resolver sobre una orden ya confirmada:
     * definir monto y forma de los que faltan, y poner el número a los eCheq que el banco emitió.
     *
     * Las filas sin `id_pago_parcial` son plan puro: todavía no existe el instrumento.
     */
    public function listarPendientesDeNumero($idBanco = null)
    {
        // 1) Fechas del plan sin pago emitido.
        $planificados = DB::table('tb_tes_fecha_probable_pago as fp')
            ->join('tb_tes_pago as p', 'p.id_pago', '=', 'fp.id_pago')
            ->join('tb_tes_orden_pago as o', 'o.id_orden_pago', '=', 'p.id_orden_pago')
            ->whereNotExists(function ($q) {
                $q->select(DB::raw(1))
                    ->from('tb_tes_pago_parcial as pp')
                    ->whereColumn('pp.id_fecha_probable', 'fp.id_fecha_probable');
            })
            ->where('p.id_estado_orden_pago', '!=', TestOrdenPagoRepository::ESTADO_OPA_RECHAZADO)
            ->whereIn('o.id_estado_orden_pago', $this->estadosOpaViva())
            ->select([
                'fp.id_fecha_probable',
                'fp.fecha_probable_pago',
                'fp.orden_cuotas',
                'p.id_pago',
                'o.id_orden_pago',
                'o.num_orden_pago',
                'o.monto_orden_pago',
                'o.tipo_factura',
                'o.id_proveedor',
                'o.id_prestador',
            ])
            ->orderBy('o.num_orden_pago')
            ->orderBy('fp.orden_cuotas')
            ->get();

        // 2) eCheq ya definidos, esperando el número del banco.
        $sinNumero = TesPagosParciales::query()
            ->join('tb_tes_pago as p', 'p.id_pago', '=', 'tb_tes_pago_parcial.id_pago')
            ->join('tb_tes_orden_pago as o', 'o.id_orden_pago', '=', 'p.id_orden_pago')
            ->where('tb_tes_pago_parcial.id_estado_instrumento', self::PENDIENTE_EMISION)
            ->when(!is_null($idBanco), fn($q) => $q->where('tb_tes_pago_parcial.id_banco_emisor', $idBanco))
            ->whereIn('o.id_estado_orden_pago', $this->estadosOpaViva())
            ->select([
                'tb_tes_pago_parcial.*',
                'o.id_orden_pago',
                'o.num_orden_pago',
                'o.monto_orden_pago',
                'o.tipo_factura',
                'o.id_proveedor',
                'o.id_prestador',
            ])
            ->with(['bancoEmisor', 'formaPago', 'pago.opa.proveedor', 'pago.opa.prestador'])
            ->get();

        return ['planificados' => $planificados, 'sin_numero' => $sinNumero];
    }

    /** Estados en los que una OP sigue viva y sus pagos pueden trabajarse. */
    private function estadosOpaViva(): array
    {
        return [
            TestOrdenPagoRepository::ESTADO_OPA_PENDIENTE,
            TestOrdenPagoRepository::ESTADO_OPA_APROBADO,
            TestOrdenPagoRepository::ESTADO_OPA_EN_PROCESO,
            TestOrdenPagoRepository::ESTADO_OPA_PAGO_PARCIAL,
        ];
    }

    /**
     * eCheq ya EMITIDOS, esperando que el banco los debite.
     *
     * Es la contracara del listado de pendientes de número: acá están los que ya salieron y sobre
     * los que corresponde acreditar (cuando el banco confirma) o rechazar (carga manual).
     *
     * Se incluyen los ACREDITADOS de los últimos días para que quede a la vista lo recién
     * confirmado y se pueda rechazar si el eCheq vuelve después.
     */
    public function listarEmitidos($idBanco = null)
    {
        return TesPagosParciales::query()
            ->select([
                'tb_tes_pago_parcial.id_pago_parcial',
                'tb_tes_pago_parcial.id_pago',
                'tb_tes_pago_parcial.numero_echeq',
                'tb_tes_pago_parcial.monto_pago',
                'tb_tes_pago_parcial.fecha_emision_echeq',
                'tb_tes_pago_parcial.fecha_confirma_pago',
                'tb_tes_pago_parcial.id_banco_emisor',
                'tb_tes_pago_parcial.id_estado_instrumento',
                'tb_tes_pago_parcial.motivo_rechazo',
                'tb_tes_orden_pago.id_orden_pago',
                'tb_tes_orden_pago.num_orden_pago',
                'tb_tes_orden_pago.tipo_factura',
                'tb_tes_orden_pago.id_proveedor',
                'tb_tes_orden_pago.id_prestador',
            ])
            ->join('tb_tes_pago', 'tb_tes_pago.id_pago', '=', 'tb_tes_pago_parcial.id_pago')
            ->join('tb_tes_orden_pago', 'tb_tes_orden_pago.id_orden_pago', '=', 'tb_tes_pago.id_orden_pago')
            ->whereIn('tb_tes_pago_parcial.id_estado_instrumento', [self::EMITIDO, self::ACREDITADO])
            ->when(!is_null($idBanco), fn($q) => $q->where('tb_tes_pago_parcial.id_banco_emisor', $idBanco))
            ->with(['bancoEmisor', 'estadoInstrumento', 'pago.opa.proveedor', 'pago.opa.prestador'])
            // Los que todavía esperan acreditación van primero: son los que requieren acción.
            ->orderBy('tb_tes_pago_parcial.id_estado_instrumento')
            ->orderBy('tb_tes_pago_parcial.fecha_emision_echeq')
            ->get();
    }

    /**
     * Nombre del beneficiario de una OP, para los listados y comprobantes.
     *
     * El tipo lo decide `tipo_factura` de la OP, no la presencia de `id_proveedor`/`id_prestador`:
     * hay filas sucias con **los dos** cargados, y elegir por coalesce devuelve el equivocado.
     *
     * Devuelve un texto siempre: en las OPs viejas cualquiera de las dos relaciones puede venir
     * en null, y un listado no es lugar para reventar.
     */
    public static function nombreBeneficiario($opa): string
    {
        if (is_null($opa)) {
            return 'SIN BENEFICIARIO';
        }

        $ente = strtoupper((string) $opa->tipo_factura) === 'PROVEEDOR'
            ? $opa->proveedor
            : $opa->prestador;

        $ente = $ente ?? $opa->proveedor ?? $opa->prestador;

        if (is_null($ente)) {
            return 'SIN BENEFICIARIO';
        }

        $cuit  = trim((string) ($ente->cuit ?? ''));
        $razon = trim((string) ($ente->razon_social ?? ''));

        return trim($cuit === '' ? $razon : "{$cuit} - {$razon}") ?: 'SIN BENEFICIARIO';
    }
}
