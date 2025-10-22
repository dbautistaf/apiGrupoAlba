<?php

namespace App\Http\Controllers\Tesoreria\Services;

use App\Http\Controllers\Contabilidad\Repository\AsientoContableRepository;
use App\Http\Controllers\Contabilidad\Repository\FormaPagoCuentaContableRepository;
use App\Http\Controllers\Contabilidad\Repository\PeriodosContablesRepository;
use App\Http\Controllers\Contabilidad\Repository\ProveedorPlanesCuentaRepository;
use App\Http\Controllers\facturacion\repository\FacturaRepository;
use App\Http\Controllers\Tesoreria\Dto\PagosDto;
use App\Http\Controllers\Tesoreria\Repository\TesCuentaCatalogoRepository;
use App\Http\Controllers\Tesoreria\Repository\TesCuentasBancariasRepository;
use App\Http\Controllers\Tesoreria\Repository\TesPagosRepository;
use App\Http\Controllers\Tesoreria\Repository\TestOrdenPagoRepository;
use App\Http\Controllers\Utils\CorrelativosOspfRepository;
use App\Http\Controllers\Utils\GeneradorCodigosUtils;
use App\Http\Controllers\Utils\ManejadorDeArchivosUtils;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TesPagosController extends Controller
{
    private $periodoContableRepositorio;
    private $periodoContableActivo;
    private $proveedorPlanesCuentaRepository;
    private $formaPagoCuentaContableRepository;
    private $correlativosOspfRepository;

    public function __construct(
        PeriodosContablesRepository $periodoContableRepositorio,
        ProveedorPlanesCuentaRepository $proveedorPlanesCuentaRepository,
        FormaPagoCuentaContableRepository $formaPagoCuentaContableRepository,
        CorrelativosOspfRepository $correlativosOspfRepository
    ) {
        $this->periodoContableRepositorio = $periodoContableRepositorio;
        $this->proveedorPlanesCuentaRepository = $proveedorPlanesCuentaRepository;
        $this->formaPagoCuentaContableRepository = $formaPagoCuentaContableRepository;
        $this->correlativosOspfRepository = $correlativosOspfRepository;
        $this->periodoContableActivo = $this->periodoContableRepositorio->findByPeriodoContableActivo();
    }


    public function getListarTipoFormaPago(TesCuentaCatalogoRepository $repository)
    {
        return response()->json($repository->findByListTipoFormaPagos());
    }

    public function getCrearPago(Request $request, TesPagosRepository $pago, TestOrdenPagoRepository $opa, TesCuentasBancariasRepository $cuenta, GeneradorCodigosUtils $generadorCodigos)
    {
        $data = $request->all();
        try {
            DB::beginTransaction();
            foreach ($data as $param) {
                //@SI LA CUENTA ESTA INACTIVA NOTIFICAMOS
                if ($cuenta->findByVerificarEstadoCuenta($param->id_cuenta_bancaria, '0')) {
                    DB::rollBack();
                    return response()->json(['message' => 'La cuenta seleccionada se encuentra <b>BLOQUEADA</b>'], 409);
                }

                //@VALIDAMOS QUE LA OPA ESTE EN ESTADO PENDIENTE
                if (!$opa->findByExistsOpaEstado($param->id_orden_pago, '1')) {
                    DB::rollBack();
                    return response()->json(['message' => 'La OPA se encuentra bloqueado.'], 409);
                }
                //@GENERAMOS EL PAGO
                $boletaPago = $pago->findByCrearPago($param);
                //@ASIGNAMOS CODDIGO BARRAS
                $codigoVerificado = $generadorCodigos->getGenerarCodigoUnico($boletaPago->id_pago);
                $pago->findByAsignarCodigoVerificacion($boletaPago->id_pago, $codigoVerificado);
                //@ACTUALIZAMOS ESTADO DE LA OPA *[4] EN PROCESO*
                $opa->findByUpdateEstado($param->id_orden_pago, 4);
                $opa->findByConfirmarFechaProbablePago($param->id_orden_pago, $param->fecha_probable_pago, $param->cuotas);
                $opa->findByConfirmarPagoEmergencia($param->id_orden_pago, $param->pago_emergencia);
            };
            DB::commit();
            return response()->json(['message' => 'Opa confirmada correctamente, enviada a Pagos']);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'code' => $th->getCode(),
                'message' => $th->getMessage()
            ], 500);
        }
    }

    public function getListarPagos(Request $request, TesPagosRepository $pago)
    {
        $data = [];
        $data = $pago->findByListPagosFiltroPrincipal($request);

        return response()->json($data);
    }

    public function getConfirmarPago(
        Request $request,
        TesPagosRepository $pago,
        TestOrdenPagoRepository $opa,
        TesCuentasBancariasRepository $cuenta,
        ManejadorDeArchivosUtils $storage,
        GeneradorCodigosUtils $generadorCodigos,
        FacturaRepository $facturaRepository,
        AsientoContableRepository $asientoContableRepository
    ) {
        try {

            DB::beginTransaction();
            $params = json_decode($request->data);
            $opaFactus = null;
            $monto_Validar = $params->anticipo == '1' ? $params->monto_anticipado : $params->monto_pago;

            // @VERIFICAMOS SI TENEMOS UN FONDOS EN LA CUENTA DE PAGO
            if (!$cuenta->findByVerificarSaldoCuenta($params->id_cuenta_bancaria, $monto_Validar)) {
                DB::rollBack();
                return response()->json(['message' => 'No hemos podido procesar tu solicitud de pago porque la cuenta bancaria seleccionada no tiene fondos suficientes. Por favor, revisa tu saldo e inténtalo otra vez.'], 409);
            }
            // @CONFIRMAMOS EL PAGO || CONFIRMA MONTO
            $pagoDb = $pago->findByConfirmarPago($params);

            //@ELIMINAR COMPRANTES
            if ($params->archivos_eliminados) {
                foreach ($params->archivos_eliminados as $archivo) {
                    $pago->findByDeleteComprobantePago($archivo->id_comprobante);
                }
            }

            //@SUBIR DETALLE DE COMPROBANTES
            $arrayArchivos = $storage->findByCargaMasivaArchivos("COMP" . $params->id_pago . $params->num_pago, 'tesoreria/comprobantes_pago', $request);
            foreach ($arrayArchivos as $key) {
                $pago->findByCargarComprobantePago($key['nombre'], $params->id_pago);
            }


            // @CONFIRMAMOS LA OPA || **PAGO ANTICIPADO**
            if ($params->anticipo == '1') {
                //@OBTENER LA DATA DE LA OPA
                $dataOpa = $opa->findById($params->id_orden_pago);
                //@OBTENER LA SUMA DE LOS PAGOS ANTICIPADOS
                $sumMontosTotalAnticipados = $pago->findBySumarDetallePagosAnticipados($params->id_orden_pago);
                //@VALIDAR QUE EL ANTICIPO NO SEA MAYOR AL VALOR DE LA OPA
                if ($sumMontosTotalAnticipados > $dataOpa->monto_orden_pago) {
                    DB::rollBack();
                    return response()->json(['message' => "El monto anticipado es mayor al monto total de la OPA."], 409);
                }

                if ($sumMontosTotalAnticipados == $dataOpa->monto_orden_pago) {
                    //@INCREMENTAR EL TOTAL DEL ANTICIPO EN LA OPA
                    $opaFactus = $opa->findByAnticipoPago($params->id_orden_pago, $sumMontosTotalAnticipados);
                    //@CONFIRMAMOS LA OPA YA QUE SE PAGO EN SU TOTALIDAD
                    $opa->findByConfirmarEstado($params->id_orden_pago, $pagoDb->fecha_procesamiento, '5');
                    //@CONFIRMAMOS A ESTADO PAGADO LA FACTURA
                    $facturaRepository->findByUpdateFactusPagoId($opaFactus->id_factura, '1');
                } else {
                    //@INCREMENTAR EL TOTAL DEL ANTICIPO EN LA OPA
                    $opa->findByAnticipoPago($params->id_orden_pago, $sumMontosTotalAnticipados);
                    //@CREAMOS UN PAGO DE DEUDA DE LA OPA ES DECIR DE LO RESTANTE
                    $totalDebe = $dataOpa->monto_orden_pago - $sumMontosTotalAnticipados;
                    $boletaPago = $pago->findByCrearPago(new PagosDto(
                        $params->id_orden_pago,
                        $params->id_cuenta_bancaria,
                        $params->fecha_probable_pago,
                        '1',
                        null,
                        $params->id_forma_pago,
                        $totalDebe,
                        null,
                        1,
                        $dataOpa->monto_orden_pago,
                        '1',
                        $params->fecha_confirma_pago
                    ));
                    //@ASIGNAMOS CODDIGO BARRAS
                    $codigoVerificado = $generadorCodigos->getGenerarCodigoUnico($boletaPago->id_pago);
                    $pago->findByAsignarCodigoVerificacion($boletaPago->id_pago, $codigoVerificado);
                }

                //@VALIDAMOS SI EL MONTO DE LA OPA Y EL ANTICIPADO AU
            } else {
                //@PAGO NORMAL
                $opaFactus = $opa->findByConfirmarEstado($params->id_orden_pago, $pagoDb->fecha_confirma_pago, '5');
                //@CONFIRMAMOS A ESTADO PAGADO LA FACTURA
                $facturaRepository->findByUpdateFactusPagoId($opaFactus->id_factura, '1');
            }

            // @REGISTRAMOS EL RETIRO DE LA CUENTA BANCARIA
            $cuenta->findByRetiroCuenta($params->id_cuenta_bancaria, $monto_Validar);
            // @REGISTRAMOS EL MOVIMIENTO DE LA CUENTA BANCARIA
            $cuenta->findByRegistrarMovimiento($params->id_cuenta_bancaria, $monto_Validar, 'EGRESO', $params->id_pago, null, 'OPA');

            DB::commit();
            return response()->json(['message' => 'El Pago ha sido confirmado y procesado con éxito.']);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'code' => $th->getCode(),
                'message' => $th->getMessage()
            ], 500);
        }
    }

    public function getAnularPago(
        Request $request,
        TesPagosRepository $pago,
        TestOrdenPagoRepository $opa
    ) {
        try {
            DB::beginTransaction();
            // @ANULAMOS EL PAGO
            $pago->findByAnularPago($request->id_pago, $request->motivo_rechazo);
            // @ANULAMOS LA OPA
            $opa->findByUpdateEstado($request->id_orden_pago, '3', $request->motivo_rechazo);
            DB::commit();
            return response()->json(['message' => 'El Pago ha sido anulado con éxito.']);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'code' => $th->getCode(),
                'message' => $th->getMessage()
            ], 500);
        }
    }

    public function getVerAdjunto(ManejadorDeArchivosUtils $storageFile, Request $request)
    {
        $path = "tesoreria/comprobantes_pago/";
        // $data = $pago->findById($request->id);
        $anioTrabaja = Carbon::parse($request->fecha_registra)->year;
        $path .= "{$anioTrabaja}/$request->nombre_archivo";

        return $storageFile->findByObtenerArchivo($path);
    }

    public function getListarDetallePago(Request $request, TesPagosRepository $repoPago)
    {
        return response()->json($repoPago->findByListDetallePagosAnticipadosConfirmados($request->id), 200);
    }
}
