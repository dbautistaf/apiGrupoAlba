<?php

namespace App\Http\Controllers\Rentabilidad;

use App\Exports\RentabilidadExport;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as RoutingController;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class RentabilidadController extends RoutingController
{
    //
    public function getDatosRentabilidad(Request $request)
    {
        $anio = $request->anio;
        $unidadNegocio = $request->unidad;
        $cuil = $request->cuil;
        $cuit = $request->cuit;

        $meses = ['12', '11', '10', '09', '08', '07', '06', '05', '04', '03', '02', '01'];
        $sumSelects = [];

        foreach ($meses as $mes) {
            $periodo = $anio . $mes;
            $sumSelects[] = DB::raw("SUM(CASE WHEN b.periodo = '$periodo' THEN b.importe ELSE 0 END) AS aporte_$mes");
            $sumSelects[] = DB::raw("SUM(CASE WHEN b.periodo = '$periodo' THEN b.importe * 2 ELSE 0 END) AS contrib_$mes");
        }

        if ($cuil != '' || $cuit != '') {
            $query = DB::table('tb_padron as a')
                ->leftJoin('tb_transferencias as b', function ($join) use ($anio) {
                    $join->on('a.cuil_tit', '=', 'b.cuitapo')
                        ->whereRaw("LEFT(b.periodo, 2) = ?", [$anio]);
                })
                ->where('a.apellidos', '<>', ' ');
            if (!empty($cuil)) {
                $query->where('a.cuil_tit', $cuil);
            } elseif (!empty($cuit)) {
                $query->where('b.cuitcont', $cuit);
            }
            $query->select(array_merge([
                'a.cuil_tit',
                'a.dni',
                'a.nombre',
                'a.apellidos',
                'a.fe_alta',
                'a.fe_baja',
                'a.observaciones'
            ], $sumSelects));
            $query->groupBy('a.cuil_tit', 'a.dni', 'a.nombre', 'a.apellidos', 'a.fe_alta', 'a.fe_baja', 'a.observaciones');
            return $query;
        } else {
            if ($unidadNegocio=='') {
                return response()->json(['message' => 'Seleccione Unidad de negocio'], 500);
            }
            $query = DB::table('tb_padron as a')
                ->leftJoin('tb_transferencias as b', function ($join) use ($anio) {
                    $join->on('a.cuil_tit', '=', 'b.cuitapo')
                        ->whereRaw("LEFT(b.periodo, 2) = ?", [$anio]);
                })
                ->where('a.id_unidad_negocio', $unidadNegocio)
                ->where('a.apellidos', '<>', ' ')
                ->select(array_merge([
                    'a.cuil_tit',
                    'a.dni',
                    'a.nombre',
                    'a.apellidos',
                    'a.fe_alta',
                    'a.fe_baja',
                    'a.observaciones'
                ], $sumSelects))
                ->groupBy('a.cuil_tit', 'a.dni', 'a.nombre', 'a.apellidos', 'a.fe_alta', 'a.fe_baja', 'a.observaciones')
                ->orderBy('a.cuil_tit')
                ->orderBy('a.dni')
                ->get();
            return $query;
        }
    }


    public function exportarRentabilidad(Request $request)
    {
        if ($request->cuil != '' || $request->cuit != '') {
            return Excel::download(new RentabilidadExport($request), 'Rentabilidad.xlsx');
        } else {
            if ($request->unidad == '') {
                return response()->json(['message' => 'Seleccione Unidad de negocio'], 500);
            }
            return Excel::download(new RentabilidadExport($request), 'Rentabilidad.xlsx');
        }
    }
}
