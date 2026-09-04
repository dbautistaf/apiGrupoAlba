<?php

namespace App\Http\Controllers\Tesoreria\Repository;

use App\Models\Tesoreria\TesCuentasBancariasEntity;
use App\Models\Tesoreria\TesOrdenPagoEntity;
use App\Models\Tesoreria\TesPagoEntity;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

/**
 * Ciclo de vida del INSTRUMENTO de pago (eCheq / transferencia).
 *
 * El instrumento vive en tb_tes_pago, extendida con `id_estado_instrumento`, `numero_echeq`,
 * `fecha_emision_echeq` e `id_banco_emisor`. Se extendió esa tabla en vez de crear una nueva
 * porque la contabilidad vincula cada asiento con su pago por `id_pago`.
 *
 * MÁQUINA DE ESTADOS (docs/circuito-pagos/plan-fase1-pagos.md §2.3):
 *
 *   BORRADOR (1)          Pagos definió el instrumento (monto y fecha). La OP todavía no se
 *                         imprimió ni se mandó a Tesorería.
 *        |
 *        v  marcarPendienteEmision()  — se imprime la OP inicial, SIN números
 *   PENDIENTE_EMISION (2) Esperando que el banco emita y asigne el número. Acá el número se
 *                         puede ir cargando de a uno (borrador de número): se guarda y se
 *                         valida al instante, pero el estado no avanza hasta confirmar.
 *        |
 *        v  confirmarEmisionDeOpa()   — se confirman TODOS los números de la OP juntos
 *   EMITIDO (3)           Números cargados y confirmados. Acá se recalcula el estado de la OP.
 *        |
 *        +--> ACREDITADO (4)  la conciliación bancaria confirmó el débito
 *        +--> RECHAZADO (5)   el eCheq volvió — LO CARGA EL USUARIO A MANO (confirmado por
 *                             negocio 2026-09-03), no sale de la conciliación
 *        +--> ANULADO (6)
 *
 * Un eCheq cubre UNA sola OP: el número es único en toda la tabla, garantizado por el índice
 * uq_numero_echeq además de la validación aplicativa.
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

    /**
     * Crea los instrumentos de pago de una OP. Nacen SIN número: el banco lo asigna recién al
     * emitirlos. Lo único que carga Pagos acá es el monto y la fecha de cada uno.
     *
     * @param array $instrumentos  [['monto' => float, 'fecha' => 'Y-m-d',
     *                               'id_forma_pago' => int, 'id_cuenta_bancaria' => ?int,
     *                               'id_banco_emisor' => ?int], ...]
     */
    public function crearInstrumentos($idOpa, array $instrumentos): array
    {
        $opa = TesOrdenPagoEntity::find($idOpa);

        if (is_null($opa)) {
            throw new \Exception("No se encontró la orden de pago {$idOpa}.");
        }

        if ((int) $opa->id_estado_orden_pago === TestOrdenPagoRepository::ESTADO_OPA_RECHAZADO) {
            throw new \Exception(
                "La orden de pago {$opa->num_orden_pago} está anulada: no se le pueden agregar pagos."
            );
        }

        if (empty($instrumentos)) {
            throw new \Exception('No se recibió ningún instrumento de pago.');
        }

        $creados = [];

        foreach ($instrumentos as $i) {
            $idBanco = $this->resolverBancoEmisor(
                $i['id_cuenta_bancaria'] ?? null,
                $i['id_banco_emisor'] ?? null
            );

            $monto = (float) ($i['monto'] ?? 0);

            if ($monto <= 0) {
                throw new \Exception('El monto de cada pago tiene que ser mayor a cero.');
            }

            if (empty($i['fecha'])) {
                throw new \Exception('Cada pago necesita su fecha.');
            }

            $creados[] = TesPagoEntity::create([
                'id_orden_pago'         => $idOpa,
                'id_cuenta_bancaria'    => $i['id_cuenta_bancaria'] ?? null,
                'fecha_registra'        => $this->fechaActual,
                'fecha_confirma_pago'   => null,
                'monto_pago'            => $monto,
                'monto_opa'             => $monto,
                'anticipo'              => 0,
                'monto_anticipado'      => 0,
                'recursor'              => 0,
                'pago_emergencia'       => 0,
                'id_forma_pago'         => $i['id_forma_pago'] ?? self::FORMA_PAGO_ECHEQ,
                'id_estado_instrumento' => self::BORRADOR,
                // El catálogo viejo de la OPA sigue poblándose para no romper el histórico
                // ni las pantallas que todavía lo leen.
                'id_estado_orden_pago'  => TestOrdenPagoRepository::ESTADO_OPA_PENDIENTE,
                'id_usuario'            => $this->user->cod_usuario ?? null,
                'tipo_factura'          => $opa->tipo_factura ?? 'PRESTADOR',
                'fecha_probable_pago'   => $i['fecha'],
                'id_banco_emisor'       => $idBanco,
            ]);
        }

        return $creados;
    }

    /**
     * Resuelve el banco emisor del eCheq.
     *
     * Si vino la cuenta bancaria, el banco lo determina la cuenta: es la única fuente que no
     * puede contradecirse a sí misma. Si además mandaron un banco explícito y NO coincide, se
     * corta — es un dato inconsistente y elegir uno en silencio dejaría el listado por banco
     * mal agrupado sin que nadie se entere.
     *
     * El banco explícito sin cuenta es válido: el catálogo de cuentas está incompleto (faltan
     * Galicia, ICBC y Nación), así que hay eCheq emitidos desde bancos todavía sin cuenta.
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
     * Marca los instrumentos de una OP como pendientes de emisión: es el momento en que se
     * imprime la OP inicial (sin números) y se manda a Tesorería.
     */
    public function marcarPendienteEmision($idOpa): int
    {
        return TesPagoEntity::where('id_orden_pago', $idOpa)
            ->where('id_estado_instrumento', self::BORRADOR)
            ->update(['id_estado_instrumento' => self::PENDIENTE_EMISION]);
    }

    /**
     * ¿El número de eCheq está libre? La unicidad es GLOBAL: un eCheq cubre una única OP.
     *
     * Se usa para la validación en vivo mientras el usuario tipea. El índice uq_numero_echeq
     * es la garantía real; esto es para poder avisar antes de que reviente.
     *
     * `$idPagoExcluir` permite revalidar el propio número al editarlo sin chocar consigo mismo.
     */
    public function numeroEcheqDisponible(?string $numero, $idPagoExcluir = null): bool
    {
        $numero = trim((string) $numero);

        if ($numero === '') {
            return false;
        }

        return !TesPagoEntity::where('numero_echeq', $numero)
            ->when(!is_null($idPagoExcluir), fn($q) => $q->where('id_pago', '!=', $idPagoExcluir))
            ->exists();
    }

    /**
     * Guarda el número de un eCheq como BORRADOR: queda persistido pero el instrumento no
     * avanza de estado hasta que se confirme la OP completa.
     *
     * El número se guarda con trim: en la columna vieja `num_cheque` hay valores con espacios
     * al borde, y no queremos arrastrar eso a la nueva.
     */
    public function guardarBorradorNumero($idPago, ?string $numero): TesPagoEntity
    {
        $pago = TesPagoEntity::find($idPago);

        if (is_null($pago)) {
            throw new \Exception("No se encontró el pago {$idPago}.");
        }

        if (!in_array((int) $pago->id_estado_instrumento, [self::BORRADOR, self::PENDIENTE_EMISION], true)) {
            throw new \Exception(
                'Solo se puede cargar el número de un pago que todavía no fue emitido.'
            );
        }

        $numero = trim((string) $numero);

        if ($numero !== '' && !$this->numeroEcheqDisponible($numero, $idPago)) {
            throw new \Exception("El número de eCheq {$numero} ya está usado en otro pago.");
        }

        $pago->numero_echeq = $numero === '' ? null : $numero;
        $pago->save();

        return $pago;
    }

    /**
     * Confirma la emisión de TODOS los eCheq de una OP, en un solo acto.
     *
     * Exige que ninguno haya quedado sin número: el requerimiento pide que se carguen todos
     * juntos y se confirmen de una. Devuelve la cantidad de instrumentos que pasaron a EMITIDO.
     */
    public function confirmarEmisionDeOpa($idOpa, TestOrdenPagoRepository $opaRepo): int
    {
        return DB::transaction(function () use ($idOpa, $opaRepo) {
            $pendientes = TesPagoEntity::where('id_orden_pago', $idOpa)
                ->whereIn('id_estado_instrumento', [self::BORRADOR, self::PENDIENTE_EMISION])
                ->get();

            if ($pendientes->isEmpty()) {
                throw new \Exception('Esta orden de pago no tiene eCheq pendientes de emisión.');
            }

            $sinNumero = $pendientes->filter(fn($p) => empty(trim((string) $p->numero_echeq)));

            if ($sinNumero->isNotEmpty()) {
                throw new \Exception(
                    'Faltan cargar ' . $sinNumero->count() . ' número(s) de eCheq. '
                    . 'Se confirman todos juntos.'
                );
            }

            foreach ($pendientes as $p) {
                $p->id_estado_instrumento = self::EMITIDO;
                $p->fecha_emision_echeq   = $this->fechaActual->toDateString();
                $p->save();
            }

            // El estado de la OP se deriva de lo pagado; emitir todavía no acredita, así que
            // esto normalmente la deja igual. Se recalcula igual para no depender de supuestos.
            $opaRepo->recalcularEstadoOpa($idOpa);

            return $pendientes->count();
        });
    }

    /**
     * Marca un eCheq como ACREDITADO. Es el único evento del ciclo que llega desde la
     * conciliación bancaria; dispara el asiento que salda la cuenta puente (fase 2).
     */
    public function marcarAcreditado($idPago, $fechaAcreditacion, TestOrdenPagoRepository $opaRepo): TesPagoEntity
    {
        return DB::transaction(function () use ($idPago, $fechaAcreditacion, $opaRepo) {
            $pago = TesPagoEntity::find($idPago);

            if (is_null($pago)) {
                throw new \Exception("No se encontró el pago {$idPago}.");
            }

            if ((int) $pago->id_estado_instrumento !== self::EMITIDO) {
                throw new \Exception('Solo se puede acreditar un eCheq que esté emitido.');
            }

            $pago->id_estado_instrumento = self::ACREDITADO;
            $pago->fecha_confirma_pago   = $fechaAcreditacion;
            // El catálogo viejo se mantiene en sincronía para las pantallas que aún lo leen.
            $pago->id_estado_orden_pago  = TestOrdenPagoRepository::ESTADO_OPA_PAGADO;
            $pago->save();

            $opaRepo->recalcularEstadoOpa($pago->id_orden_pago);

            return $pago;
        });
    }

    /**
     * Marca un eCheq como RECHAZADO. **Carga manual del usuario** — confirmado por negocio el
     * 2026-09-03: el rechazo no llega desde la conciliación bancaria.
     *
     * Deja el instrumento fuera del cómputo de lo pagado, así que al recalcular, la OP vuelve
     * a PENDIENTE o PAGO PARCIAL según lo que quede. De este mismo evento sale el asiento de
     * reversión del punto 9 (fase 2).
     */
    public function marcarRechazado($idPago, ?string $motivo, TestOrdenPagoRepository $opaRepo): TesPagoEntity
    {
        return DB::transaction(function () use ($idPago, $motivo, $opaRepo) {
            $pago = TesPagoEntity::find($idPago);

            if (is_null($pago)) {
                throw new \Exception("No se encontró el pago {$idPago}.");
            }

            if (!in_array((int) $pago->id_estado_instrumento, [self::EMITIDO, self::ACREDITADO], true)) {
                throw new \Exception('Solo se puede rechazar un eCheq que haya sido emitido.');
            }

            $pago->id_estado_instrumento = self::RECHAZADO;
            $pago->motivo_rechazo        = $motivo;
            $pago->fecha_rechazo         = $this->fechaActual;
            // El catálogo viejo usa 3 para "rechazado", y es lo que mira la guarda de pagos.
            $pago->id_estado_orden_pago  = TestOrdenPagoRepository::ESTADO_OPA_RECHAZADO;
            $pago->fecha_confirma_pago   = null;
            $pago->save();

            $opaRepo->recalcularEstadoOpa($pago->id_orden_pago);

            return $pago;
        });
    }

    /**
     * OPs con eCheq pendientes de número, agrupadas por banco emisor.
     *
     * Es el listado exportable que pide el requerimiento (Excel y PDF): N.º de OP, proveedor,
     * banco, N.º de eCheq, monto y fecha de pago.
     */
    public function listarPendientesDeNumero($idBanco = null)
    {
        return TesPagoEntity::query()
            ->select([
                'tb_tes_pago.id_pago',
                'tb_tes_pago.id_orden_pago',
                'tb_tes_pago.numero_echeq',
                'tb_tes_pago.monto_pago',
                'tb_tes_pago.fecha_probable_pago',
                'tb_tes_pago.id_banco_emisor',
                'tb_tes_pago.id_estado_instrumento',
                'tb_tes_orden_pago.num_orden_pago',
                'tb_tes_orden_pago.id_estado_orden_pago',
                'tb_tes_orden_pago.id_proveedor',
                'tb_tes_orden_pago.id_prestador',
            ])
            ->join('tb_tes_orden_pago', 'tb_tes_orden_pago.id_orden_pago', '=', 'tb_tes_pago.id_orden_pago')
            ->whereIn('tb_tes_pago.id_estado_instrumento', [self::BORRADOR, self::PENDIENTE_EMISION])
            // Solo OPs vivas: pendientes o parcialmente pagadas, como pide el requerimiento.
            ->whereIn('tb_tes_orden_pago.id_estado_orden_pago', [
                TestOrdenPagoRepository::ESTADO_OPA_PENDIENTE,
                TestOrdenPagoRepository::ESTADO_OPA_PAGO_PARCIAL,
            ])
            ->when(!is_null($idBanco), fn($q) => $q->where('tb_tes_pago.id_banco_emisor', $idBanco))
            ->with(['opa.proveedor', 'opa.prestador', 'bancoEmisor'])
            ->orderBy('tb_tes_pago.id_banco_emisor')
            ->orderBy('tb_tes_orden_pago.num_orden_pago')
            ->get();
    }

    /**
     * Nombre del beneficiario de una OP, para los listados y comprobantes.
     *
     * El tipo lo decide `tipo_factura` de la OP ('PROVEEDOR' / 'PRESTADOR'), no la presencia de
     * `id_proveedor`/`id_prestador`: hay filas sucias con **los dos** cargados, y elegir por
     * coalesce devuelve el beneficiario equivocado.
     *
     * Devuelve un texto siempre. Nunca desreferencia una relación sin chequearla: en las OPs
     * viejas cualquiera de las dos puede venir en null, y un listado no es lugar para reventar.
     */
    public static function nombreBeneficiario($opa): string
    {
        if (is_null($opa)) {
            return 'SIN BENEFICIARIO';
        }

        $ente = strtoupper((string) $opa->tipo_factura) === 'PROVEEDOR'
            ? $opa->proveedor
            : $opa->prestador;

        // Si el tipo apunta a una relación vacía, se prueba la otra antes de darse por vencido.
        $ente = $ente ?? $opa->proveedor ?? $opa->prestador;

        if (is_null($ente)) {
            return 'SIN BENEFICIARIO';
        }

        $cuit  = trim((string) ($ente->cuit ?? ''));
        $razon = trim((string) ($ente->razon_social ?? ''));

        return trim($cuit === '' ? $razon : "{$cuit} - {$razon}") ?: 'SIN BENEFICIARIO';
    }
}
