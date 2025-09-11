<?php

namespace App\Http\Controllers\liquidaciones;

use App\Http\Controllers\liquidaciones\repository\LiquidacionesFacturaRepository;
use App\Models\facturacion\FacturacionDatosEntity;
use App\Models\prestadores\PrestadorEntity;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class LiquidacionesFacturaController extends Controller
{

    public function getFacturaLiquidaciones(LiquidacionesFacturaRepository $repo, Request $request)
    {
        $data = [];
        $arrayEstados = ($request->estado === '9')
            ? ['0', '1', '2', '3', '4', '5', '6', '7']
            : [$request->estado];

        $params = [
            'desde' => $request->desde,
            'hasta' => $request->hasta,
            'estado' => $arrayEstados,
            'limit' => 1000
        ];

        if (!is_null($request->cuit_prestador) && is_null($request->num_factura) && is_null($request->periodo)) {
            $data = $repo->findTopByFechaRecepcionBetweenAndEstadoAndCuitPrestadorLike($params['desde'], $params['hasta'], $params['estado'], $request->cuit_prestador, $params['limit']);
        } elseif (is_null($request->cuit_prestador) && !is_null($request->num_factura) && is_null($request->periodo)) {
            $data = $repo->findTopByFechaRecepcionBetweenAndEstadoAndNumFacturaLike($params['desde'], $params['hasta'], $params['estado'], $request->num_factura, $params['limit']);
        } elseif (is_null($request->cuit_prestador) && is_null($request->num_factura) && !is_null($request->periodo)) {
            $data = $repo->findTopByFechaRecepcionBetweenAndPeriodo($params['desde'], $params['hasta'], $params['estado'], $request->periodo, $params['limit']);
        } elseif (is_null($request->num_factura) && is_null($request->cuit_prestador) && is_null($request->periodo) && !is_null($request->cod_usuario)) {
            $data = $repo->findTopByFechaRecepcionBetweenAndUsuarioLike($params['desde'], $params['hasta'], $params['estado'], $request->cod_usuario, $params['limit']);
        } elseif(is_null($request->cuit_prestador) && is_null($request->num_factura) && is_null($request->periodo) && !is_null($request->id_locatario)){
            $data = $repo->findTopByFechaRecepcionBetweenAndLocatario($params['desde'], $params['hasta'], $params['estado'], $params['limit'],$request->id_locatario);
        } else {
            $data = $repo->findTopByFechaRecepcionBetweenAndEstado($params['desde'], $params['hasta'], $params['estado'], $params['limit']);
        }

        return response()->json($data);
    }


    public function getCabeceraFacturaLiquidacion(LiquidacionesFacturaRepository $repo, Request $request)
    {
        $factura = $repo->findByIdFactura($request->id);
        return response()->json(count($factura) > 0 ? $factura[0] : null);
    }

    public function getPeriodos()
    {
        $periodos = FacturacionDatosEntity::select('periodo')->distinct()->orderBy('periodo')->get();
        $periodos = $periodos->map(function ($item) {
            return [
                'val' => $item->periodo,
                'label' => $item->periodo
            ];
        });
        return response()->json($periodos);
    }

    public function getIvaPrestador($cuit)
    {
        $iva = PrestadorEntity::with('tipoIva')->where('cuit', $cuit)->first();

        $ivaArray = [
            [
                "cod_tipo_iva" => $iva->tipoIva->cod_tipo_iva,
                "descripcion_iva" => $iva->tipoIva->descripcion_iva
            ]
        ];

        return response()->json($ivaArray);
    }
}
