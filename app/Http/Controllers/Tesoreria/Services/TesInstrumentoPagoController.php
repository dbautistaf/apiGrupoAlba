<?php

namespace App\Http\Controllers\Tesoreria\Services;

use App\Http\Controllers\Tesoreria\Repository\TesInstrumentoPagoRepository;
use App\Http\Controllers\Tesoreria\Repository\TestOrdenPagoRepository;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;

/**
 * Ciclo de vida del instrumento de pago (eCheq).
 *
 * Convención de códigos en este controller:
 *  - 422 → falta un dato del request.
 *  - 409 → la operación choca contra una regla de negocio (número duplicado, faltan números,
 *          el instrumento ya se emitió). El mensaje del repositorio se devuelve tal cual porque
 *          está redactado para que lo lea el usuario de Pagos.
 *  - 500 → error inesperado. Acá NO se filtra el mensaje interno.
 */
class TesInstrumentoPagoController extends Controller
{
    protected $repository;
    protected $opaRepository;

    public function __construct(
        TesInstrumentoPagoRepository $repository,
        TestOrdenPagoRepository $opaRepository
    ) {
        $this->repository    = $repository;
        $this->opaRepository = $opaRepository;
    }

    /**
     * POST /v1/tesoreria/instrumentos-pago
     * Body: { id_orden_pago, instrumentos: [{ monto, fecha, id_forma_pago?,
     *                                         id_cuenta_bancaria?, id_banco_emisor? }] }
     */
    public function getCrearInstrumentos(Request $request)
    {
        try {
            $idOpa        = $request->input('id_orden_pago');
            $instrumentos = $request->input('instrumentos', []);

            if (!$idOpa) {
                return response()->json(['message' => 'id_orden_pago es requerido'], 422);
            }

            if (!is_array($instrumentos) || empty($instrumentos)) {
                return response()->json(['message' => 'Hay que enviar al menos un instrumento de pago'], 422);
            }

            $creados = $this->repository->crearInstrumentos($idOpa, $instrumentos);

            return response()->json([
                'message' => 'Se crearon ' . count($creados) . ' pago(s) pendientes de emisión',
                'data'    => $creados,
            ], 201);
        } catch (QueryException $e) {
            Log::error('Error crear instrumentos de pago: ' . $e->getMessage());
            return response()->json(['message' => 'Error al crear los pagos'], 500);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 409);
        }
    }

    /**
     * POST /v1/tesoreria/instrumentos-pago/marcar-pendiente-emision
     * Body: { id_orden_pago }
     *
     * Es el acto de imprimir la OP inicial y mandarla a Tesorería.
     */
    public function getMarcarPendienteEmision(Request $request)
    {
        try {
            $idOpa = $request->input('id_orden_pago');

            if (!$idOpa) {
                return response()->json(['message' => 'id_orden_pago es requerido'], 422);
            }

            $n = $this->repository->marcarPendienteEmision($idOpa);

            return response()->json([
                'message' => "Se marcaron {$n} pago(s) como pendientes de emisión",
                'data'    => ['actualizados' => $n],
            ], 200);
        } catch (QueryException $e) {
            Log::error('Error marcar pendiente de emisión: ' . $e->getMessage());
            return response()->json(['message' => 'Error al actualizar los pagos'], 500);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 409);
        }
    }

    /**
     * GET /v1/tesoreria/instrumentos-pago/validar-numero?numero=&id_pago=
     *
     * Validación en vivo mientras el usuario tipea. `id_pago` es opcional y sirve para que al
     * reeditar un número, el propio pago no se cuente como duplicado.
     *
     * Devuelve 200 siempre: "no disponible" es una respuesta válida, no un error.
     */
    public function getValidarNumero(Request $request)
    {
        try {
            $numero = $request->query('numero');
            $idPago = $request->query('id_pago');

            if (is_null($numero) || trim((string) $numero) === '') {
                return response()->json(['message' => 'numero es requerido'], 422);
            }

            $disponible = $this->repository->numeroEcheqDisponible($numero, $idPago);

            return response()->json([
                'disponible' => $disponible,
                'message'    => $disponible
                    ? 'Número disponible'
                    : 'Este número de eCheq ya está usado en otro pago',
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error validar número de eCheq: ' . $e->getMessage());
            return response()->json(['message' => 'Error al validar el número'], 500);
        }
    }

    /**
     * PUT /v1/tesoreria/instrumentos-pago/{idPago}/numero
     * Body: { numero }
     *
     * Guarda el número como borrador. El instrumento NO avanza de estado.
     */
    public function getGuardarNumero(Request $request, $idPago)
    {
        try {
            $pago = $this->repository->guardarBorradorNumero($idPago, $request->input('numero'));

            return response()->json([
                'message' => 'Número guardado',
                'data'    => $pago,
            ], 200);
        } catch (QueryException $e) {
            Log::error('Error guardar número de eCheq: ' . $e->getMessage());
            return response()->json(['message' => 'Error al guardar el número'], 500);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 409);
        }
    }

    /**
     * POST /v1/tesoreria/instrumentos-pago/confirmar-emision
     * Body: { id_orden_pago }
     *
     * Confirma TODOS los eCheq de la OP juntos. Falla si alguno quedó sin número.
     */
    public function getConfirmarEmision(Request $request)
    {
        try {
            $idOpa = $request->input('id_orden_pago');

            if (!$idOpa) {
                return response()->json(['message' => 'id_orden_pago es requerido'], 422);
            }

            $n = $this->repository->confirmarEmisionDeOpa($idOpa, $this->opaRepository);

            return response()->json([
                'message' => "Se confirmaron {$n} eCheq",
                'data'    => ['emitidos' => $n],
            ], 200);
        } catch (QueryException $e) {
            Log::error('Error confirmar emisión de eCheq: ' . $e->getMessage());
            return response()->json(['message' => 'Error al confirmar la emisión'], 500);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 409);
        }
    }

    /**
     * POST /v1/tesoreria/instrumentos-pago/{idPago}/acreditar
     * Body: { fecha_acreditacion }
     */
    public function getAcreditar(Request $request, $idPago)
    {
        try {
            $fecha = $request->input('fecha_acreditacion');

            if (!$fecha) {
                return response()->json(['message' => 'fecha_acreditacion es requerida'], 422);
            }

            $pago = $this->repository->marcarAcreditado($idPago, $fecha, $this->opaRepository);

            return response()->json([
                'message' => 'eCheq acreditado',
                'data'    => $pago,
            ], 200);
        } catch (QueryException $e) {
            Log::error('Error acreditar eCheq: ' . $e->getMessage());
            return response()->json(['message' => 'Error al acreditar el eCheq'], 500);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 409);
        }
    }

    /**
     * POST /v1/tesoreria/instrumentos-pago/{idPago}/rechazar
     * Body: { motivo_rechazo }
     *
     * Carga MANUAL del rechazo: no llega desde la conciliación bancaria.
     */
    public function getRechazar(Request $request, $idPago)
    {
        try {
            $motivo = $request->input('motivo_rechazo');

            if (is_null($motivo) || trim((string) $motivo) === '') {
                return response()->json(['message' => 'El motivo del rechazo es requerido'], 422);
            }

            $pago = $this->repository->marcarRechazado($idPago, $motivo, $this->opaRepository);

            return response()->json([
                'message' => 'eCheq marcado como rechazado',
                'data'    => $pago,
            ], 200);
        } catch (QueryException $e) {
            Log::error('Error rechazar eCheq: ' . $e->getMessage());
            return response()->json(['message' => 'Error al rechazar el eCheq'], 500);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 409);
        }
    }

    /**
     * GET /v1/tesoreria/instrumentos-pago/pendientes-numero?id_banco=
     *
     * OPs vigentes con eCheq todavía sin número, agrupadas por banco emisor.
     */
    public function getPendientesDeNumero(Request $request)
    {
        try {
            $data = $this->repository->listarPendientesDeNumero($request->query('id_banco'));
            return response()->json($data, 200);
        } catch (\Exception $e) {
            Log::error('Error listar pendientes de número: ' . $e->getMessage());
            return response()->json(['message' => 'Error al listar los pagos pendientes'], 500);
        }
    }
}
