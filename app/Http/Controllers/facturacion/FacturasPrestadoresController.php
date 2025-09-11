<?php

namespace App\Http\Controllers\facturacion;

use App\Exports\Facturas\FacturaPrestadorExport;
use App\Exports\Facturas\FacturaProveedorExport;
use App\Http\Controllers\facturacion\repository\FacturasPrestadoresRepository;
use App\Http\Controllers\liquidaciones\repository\LiquidacionesDetalleRepository;
use App\Http\Controllers\liquidaciones\repository\LiquidacionesRepository;
use App\Http\Controllers\Tesoreria\Repository\TestOrdenPagoRepository;
use App\Models\Tesoreria\TesOrdenPagoEntity;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class FacturasPrestadoresController extends Controller
{
    public function getFacturasPrestadores(FacturasPrestadoresRepository $repo, Request $request)
    {
        $data = [];

        /* if ($request->estado === '9' && !is_null($request->cuit_prestador) && is_null($request->razon_social)) {
            $data = $repo->findByListCuitPrestadorCuit($request->desde, $request->hasta, $request->cuit_prestador);
        } else if ($request->estado === '9' && is_null($request->cuit_prestador) && !is_null($request->razon_social)) {
            $data = $repo->findByListCuitPrestadorRazonSocial($request->desde, $request->hasta, $request->razon_social);
        } else if ($request->estado !== '9' && is_null($request->cuit_prestador) && is_null($request->razon_social)) {
            $data = $repo->findByListEstado($request->desde, $request->hasta, $request->estado);
        } else {
            $data = $repo->findByListAlls($request->desde, $request->hasta, ['0', '1', '2', '3', '4', '5']);
    } */

        //Probando traer comprobantes con metodo nuevo
        // $data = $repo->findByListAlls($request);
        $data = $repo->findByListAllsWithComprobantes($request);

        return response()->json($data);
    }


    public function getFacturasPrestadorLiquidaciones(FacturasPrestadoresRepository $repo, Request $request)
    {
        $data = [];

        /* if ($request->estado === '9' && !is_null($request->cuit_prestador) && is_null($request->num_factura)) {
            $data = $repo->findByListCuitPrestadorCuit($request->desde, $request->hasta, $request->cuit_prestador);
        } else if ($request->estado === '9' && is_null($request->cuit_prestador) && !is_null($request->num_factura)) {
            $data = $repo->findByListCuitPrestadorAndNumFactura($request->desde, $request->hasta, $request->num_factura);
        } else if ($request->estado !== '9' && is_null($request->cuit_prestador) && is_null($request->num_factura)) {
            $data = $repo->findByListEstado($request->desde, $request->hasta, $request->estado);
        } else {
            //$data = $repo->findByListAlls($request->desde, $request->hasta, ['1', '2', '3', '0', '5']);
        } */
        $data = $repo->findByListAlls($request);
        return response()->json($data);
    }


    public function getActualizarEstadoLiquidacion(
        FacturasPrestadoresRepository $repo,
        LiquidacionesRepository $repoLiqui,
        LiquidacionesDetalleRepository $repoDetalle,
        TestOrdenPagoRepository $opa,
        Request $request
    ) {
        try {
            DB::beginTransaction();

            $liquidaciones = $repoLiqui->findByLiquidacionFactura($request->factura);

            $repo->findByUpdateEstado($request->factura, $request->estado);

            /* if (!empty($liquidaciones)) {
                $repoDetalle->findByUpdateDetalleEstado($request->estado, $liquidaciones->id_liquidacion);
            } */
            if (count($liquidaciones) > 0) {
                foreach ($liquidaciones as $key) {
                    $repoDetalle->findByUpdateDetalleEstado($request->estado, $key->id_liquidacion);
                }
            }

            $message = $request->estado === '4' ? 'Factura ANULADO correctamente'
                : ($request->estado === '2' ? 'Factura CERRADA correctamente'
                    : ($request->estado === '1' ? 'Factura se reabrio correctamente'
                        : ($request->estado === '3' ? 'Se asigno la VALORIZACION FINAL correctamente' : 'Factura en proceso de AUDITORIA')));

            if ($request->estado == '3') {

                $facturaDb = $repo->findByFacturaId($request->factura);

                $opaFactura = $opa->findByOpaFactura($request->factura, 1);

                if (!is_null($opaFactura)) {
                    $opa->findByUpdateOpaFactura(TesOrdenPagoEntity::where('id_factura', $request->factura)->first());
                } else {
                    $opa->findByCreate(new TesOrdenPagoEntity([
                        'id_proveedor' => null,
                        'id_prestador' => $facturaDb->id_prestador,
                        'monto_orden_pago' => $facturaDb->total_neto,
                        'id_moneda' => 1,
                        'fecha_emision' => $facturaDb->fecha_comprobante,
                        'fecha_vencimiento' => $facturaDb->fecha_vencimiento,
                        'fecha_probable_pago' => null,
                        'id_estado_orden_pago' => '1',
                        'monto_anticipado' => '0.00',
                        'observaciones' => '',
                        'id_factura' => $request->factura,
                        'tipo_factura' => 'PRESTADOR'
                    ]));
                }
            } else if ($request->estado == '1') {
                $facturaDb = $repo->findByFacturaId($request->factura);

                $opaFactura = $opa->findByOpaFactura($request->factura, 1);
            }
            DB::commit();
            return response()->json(["message" => $message]);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'code' => $th->getCode(),
                'message' => $th->getMessage()
            ], 500);
        }
    }

    public function getFacturaCompletaId(FacturasPrestadoresRepository $repo, Request $request)
    {
        return response()->json($repo->findByFacturaId($request->id));
    }

    public function updateDetalleCarga(FacturasPrestadoresRepository $repo, Request $request)
    {
        $repo->findByUpdateTipodetalleAndIdFactura($request->tipo, $request->id);
        return response()->json(["message" => "Tipo detalle cambiado correctamente"]);
    }

    public function updateImputacion(FacturasPrestadoresRepository $repo, Request $request)
    {
        $repo->findByUpdateImputacionAndIdFactura($request->tipo, $request->id);
        return response()->json(["message" => "Imputación contable cambiado correctamente"]);
    }

    public function getExportFacturaPrestador(Request $request)
    {
        return Excel::download(new FacturaPrestadorExport($request), 'facturacionPrestador.xlsx');
    }

    public function getExportFacturaProveedor(Request $request)
    {
        return Excel::download(new FacturaProveedorExport($request), 'facturacionProveedor.xlsx');
    }
}
