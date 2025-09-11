<?php

namespace App\Http\Controllers\Contabilidad\Services;

use App\Http\Controllers\Contabilidad\Repository\LibroDiarioRepository;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class  LibroDiarioController extends Controller
{

    public function getListarResumenDiario(Request $request, LibroDiarioRepository $libroDiarioRepository)
    {
        $data = [];
        $dtListaData = [];
        $data = $libroDiarioRepository->findListDetalleResumenDiario($request);
        foreach ($data as $value) {
            $detalle = [];
            foreach ($value->detalle as $key) {
                $detalle[] = array(
                    'cuenta' => $key->planCuenta->codigo_cuenta . ' - ' . $key->planCuenta->cuenta,
                    'debe' => (float) $key->monto_debe > 0 ? $key->monto_debe : '',
                    'haber' => (float) $key->monto_haber > 0 ? $key->monto_haber : '',
                    'recursor' => $key->recursor
                );
            }
            $dtListaData[] = array(
                'id_asiento_contable' => $value->id_asiento_contable,
                'fecha' => $value->fecha_asiento,
                'numero' => $value->numero,
                'leyenda' => $value->asiento_leyenda,
                'cuentas' => $detalle
            );
        }

        return response()->json($dtListaData);
    }
}
