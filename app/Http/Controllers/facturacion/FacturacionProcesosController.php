<?php

namespace App\Http\Controllers\facturacion;

use App\Http\Controllers\facturacion\repository\FacturaRepository;
use App\Http\Controllers\Tesoreria\Repository\TesPagosRepository;
use App\Http\Controllers\Tesoreria\Repository\TestOrdenPagoRepository;
use App\Http\Controllers\Utils\ManejadorDeArchivosUtils;
use App\Http\Controllers\Utils\GeneradorCodigosUtils;
use App\Models\facturacion\FacturacionDatosEntity;
use App\Models\Tesoreria\TesOrdenPagoEntity;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class FacturacionProcesosController extends Controller
{

    public function postProcesarFactura(
        FacturaRepository $repo,
        TestOrdenPagoRepository $tesoreria,
        TesPagosRepository $tesPagosRepository,
        ManejadorDeArchivosUtils $storageFile,
        GeneradorCodigosUtils $generadorCodigos,
        Request $request
    ) {
        DB::beginTransaction();
        try {
            $fechaActual = Carbon::now('America/Argentina/Buenos_Aires');
            $user = Auth::user();

            $cabecera = json_decode($request->cabecera);
            $detalle = json_decode($request->detalle);
            $impuestos = json_decode($request->impuestos);
            $descuentos = json_decode($request->descuentos);

            $nombre_archivo = null;
            // @SUBIR ARCHIVO

            // $nombre_archivo = $storageFile->findBycargarArchivo("FACTURA_" . $cabecera->tipo_letra . $cabecera->numero . $cabecera->sucursal, 'facturacion/comprobantes', $request);
            if (empty($cabecera->id_factura)) {

                if ($repo->findByExistsFacturaPrestadorOrPrestador($cabecera)) {
                    DB::rollBack();
                    return response()->json([
                        'message' => "La factura que intenta registrar ya éxiste"
                    ], 409);
                }

                $facturacion = $repo->findBySaveDatosFactura($cabecera, $user->cod_usuario, $nombre_archivo);

                if (count($detalle) > 0) {
                    $repo->findBySaveDetalleFactura($detalle, $facturacion->id_factura);
                }

                if (count($impuestos) > 0) {
                    $repo->findBySaveDetalleImpuestoFactura($impuestos, $facturacion->id_factura);
                }

                if (count($descuentos) > 0) {
                    $repo->findBySaveDetalleDescuentosFactura($descuentos, $facturacion->id_factura);
                }

                if (count($request->archivos) > 0) {
                    $archivosAdjuntos = $storageFile->findByCargaMasivaArchivos("FACTURA_" . $cabecera->tipo_letra . $cabecera->numero . $cabecera->sucursal, 'facturacion/comprobantes', $request);
                    $repo->findBySaveDetalleComprobantesFactura($archivosAdjuntos, $facturacion->id_factura);
                }
                /* || $facturacion->id_tipo_factura == 17 */
                if (!is_null($facturacion->id_proveedor)) {
                    $opaData = (object) [

                        // $tesoreria->findByCreate(new TesOrdenPagoEntity([
                        "id_proveedor" => $facturacion->id_tipo_factura == 16 || $facturacion->id_tipo_factura == 20 ? $facturacion->id_proveedor : null,
                        "id_prestador" => $facturacion->id_tipo_factura == 17 ? $facturacion->id_prestador : null,
                        "monto_orden_pago" => $facturacion->total_neto,
                        "id_moneda" => '1',
                        "fecha_emision" => $facturacion->fecha_comprobante,
                        "fecha_vencimiento" => $facturacion->fecha_vencimiento,
                        "fecha_probable_pago" => null,
                        "id_estado_orden_pago" => $facturacion->id_tipo_factura == 20 ? 2 : 1,
                        "monto_anticipado" => 0.00,
                        "observaciones" => '',
                        "id_factura" => $facturacion->id_factura,
                        "tipo_factura" => $facturacion->id_tipo_factura == 17 ? 'PRESTADOR' : 'PROVEEDOR'
                        // ]));
                    ];
                    $opaCreada = $tesoreria->findByCreate($opaData);

                    // Auto-crear pago para facturas tipo 20
                    if ($facturacion->id_tipo_factura == 20) {
                        $pagoData = [
                            'id_orden_pago' => $opaCreada->id_orden_pago,
                            'id_cuenta_bancaria' => 1, // Usar cuenta bancaria por defecto o configurar según necesidad
                            'fecha_confirma_pago' => $fechaActual,
                            'anticipo' => '0',
                            'comprobante' => null,
                            'id_forma_pago' => 1, // Usar forma de pago por defecto o configurar según necesidad
                            'monto_pago' => $facturacion->total_neto,
                            'observaciones' => 'Pago automático para factura tipo 20',
                            'id_estado_orden_pago' => 1, //debe ser el estado de pago
                            'monto_opa' => $facturacion->total_neto,
                            'recursor' => '0',
                            'fecha_probable_pago' => $fechaActual,
                            'tipo_factura' => 'PROVEEDOR',
                            'pago_emergencia' => '0'
                        ];

                        $boletaPago = $tesPagosRepository->findByCrearPago($pagoData);
                        $codigoVerificado = $generadorCodigos->getGenerarCodigoUnico($boletaPago->id_pago);
                        $tesPagosRepository->findByAsignarCodigoVerificacion($boletaPago->id_pago, $codigoVerificado);

                        // Actualizar estado de la OPA a "En proceso"
                        $tesoreria->findByUpdateEstado($opaCreada->id_orden_pago, 4);
                    }
                }
            } else {

                $facturacion = $repo->findByUpdateDatosFactura($cabecera, $fechaActual, $nombre_archivo);

                if (count($detalle) > 0) {
                    $repo->findByUpdateDetalleFactura($detalle, $facturacion->id_factura);
                }

                $repo->findByDeleteDetalleImpuestos($facturacion->id_factura);
                if (count($impuestos) > 0) {
                    $repo->findBySaveDetalleImpuestoFactura($impuestos, $facturacion->id_factura);
                }

                $repo->findByDeleteDetalleDescuentosFactura($facturacion->id_factura);
                if (count($descuentos) > 0) {
                    $repo->findBySaveDetalleDescuentosFactura($descuentos, $facturacion->id_factura);
                }

                if (count($request->archivos) > 0) {
                    $archivosAdjuntos = $storageFile->findByCargaMasivaArchivos("FACTURA_" . $cabecera->tipo_letra . $cabecera->numero . $cabecera->sucursal, 'facturacion/comprobantes', $request);
                    $repo->findBySaveDetalleComprobantesFactura($archivosAdjuntos, $facturacion->id_factura);
                }

                //@UPDATE OPA Y PRE-PAGO TESORERIA
                $opa = $tesoreria->findByIdFacturaEnProcesoOrPendiente($facturacion->id_factura, $facturacion->total_neto);
                if ($opa != null) {
                    $tesPagosRepository->findByUpdateOpaPagoFacturaLiquidaciones($opa->id_orden_pago, $facturacion->total_neto);
                }
            }

            DB::commit();
            return response()->json(["message" => "Factura procesada correctamente"]);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'message' => $th->getMessage()
            ], 500);
        }
    }

    public function getBuscarFacturaId(FacturaRepository $repo, Request $request)
    {
        return response()->json($repo->findByIdFactura($request->id));
    }

    public function deleteFacturaDetalle(Request $request)
    {

        $factura = FacturacionDatosEntity::find($request->id_factura);
        //$factura->estado = '4';
        $factura->delete();

        return response()->json(["message" => "La Factura N° " . $factura->num_liquidacion . " fue anulada correctamente"]);
    }

    public function getBuscarNumeroFactura(FacturaRepository $repo, Request $request)
    {
        return response()->json($repo->findByNumeroFactura($request->num_factura));
    }

    public function getListarDetalleComprobantes(FacturaRepository $repo, Request $request)
    {
        return response()->json($repo->findByListDetalleArchivos($request->id));
    }

    public function getVerAdjunto(FacturaRepository $pago, ManejadorDeArchivosUtils $storageFile, Request $request)
    {
        $path = "facturacion/comprobantes/";
        $data = $pago->findByIdDetalleArchivo($request->id);
        $anioTrabaja = Carbon::parse($data->fecha_carga)->year;
        $path .= "{$anioTrabaja}/$data->archivo";

        return $storageFile->findByObtenerArchivo($path);
    }

    public function getEliminarAdjunto(Request $request, FacturaRepository $pago)
    {
        $pago->findByIdDeleteDetalleArchivo($request->id);

        return response()->json(["message" => "Adjunto eliminado con éxito"]);
    }

    public function printComprobanteFacturacion(FacturaRepository $repo, Request $request)
    {
        $factura = $repo->findByIdFactura($request->id);

        $neto = 0.0;
        $impuestos = 0.0;

        foreach ($factura->detalle as $item) {
            $neto += $item->subtotal;
        }

        foreach ($factura->impuesto as $item) {
            $impuestos += $item->importe;
        }

        $datos = [
            "comprobante_nro" => $factura->numero,
            "tipoComprobante" => $factura->tipoComprobante->descripcion,
            "tipo_letra" => $factura->tipo_letra,
            "fecha_emision" => $factura->fecha_comprobante,
            "fecha_vencimiento" => $factura->fecha_vencimiento,
            "cuit_proveedor" => $factura->proveedor ? $factura->proveedor?->cuit : $factura->prestador?->cuit,
            "nombre_proveedor" => $factura->proveedor ? $factura->proveedor?->razon_social : $factura->prestador?->razon_social,
            "iva_proveedor" => $factura->proveedor ? $factura->proveedor?->tipoIva?->descripcion_iva : $factura->prestador?->tipoIva?->descripcion_iva,
            "periodo" => $factura->periodo,
            "tipo" => $factura->tipoFactura->descripcion,
            "sucursal" => $factura->sucursal,
            "numero" => $factura->numero,
            "detalle" => $factura->detalle,
            "impuesto" => $factura->impuesto,
            "neto" => $neto,
            "impuestos" => $impuestos,
            "descuentos" => $factura->total_debitado_liquidacion,
            "total" => $neto + $impuestos,
            "locatario" => $factura->cod_sindicato,
            'razon_social' => $factura->razonSocial,
            'id_factura' => $factura->id_factura,
            'fecha_confirma_pago' => $factura->opa?->fechapagos?->fecha_probable_pago,
            "codigo_opa" => !empty($factura->opa) > 0 ? $factura->opa->num_orden_pago : '000',
        ];

        $pdf = Pdf::loadView('comprobante-facturacion', $datos);
        $pdf->setPaper('A4');
        return $pdf->download('comprobante-facturacion' . $factura->numero . '.pdf');
    }
}
