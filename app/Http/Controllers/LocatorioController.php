<?php

namespace App\Http\Controllers;

use App\Models\LocatorioModelos;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class LocatorioController extends Controller
{
    //
    public function getoLocatorioActivos()
    {
        $locatorio =  LocatorioModelos::where('activo', '1')->get();
        return response()->json($locatorio, 200);
    }

    public function getLocatorio()
    {
        $locatorio =  LocatorioModelos::get();
        return response()->json($locatorio, 200);
    }

    public function filterLocatorio($id)
    {
        return LocatorioModelos::where('id_locatorio', $id)->first();
    }

    public function saveLocatorio(Request $request)
    {

        if ($request->id_locatorio != '') {
            $query = LocatorioModelos::where('id_locatorio', $request->id_locatorio)->first();
            $query->locatorio = $request->locatorio;
            $query->activo = $request->activo;
            $query->save();
            return response()->json(['message' => 'Datos de agente actualizado correctamente'], 200);
        } else {
            LocatorioModelos::create([
                'id_locatorio' => $request->id_locatorio,
                'locatorio' => $request->locatorio,
                'activo' => $request->activo,
            ]);
            return response()->json(['message' => 'Datos de agente registrados correctamente'], 200);
        }
    }

    public function updateEstado(Request $request)
    {
        LocatorioModelos::where('id_locatorio', $request->id)->update(['activo' => $request->activo,]);
        return response()->json(['message' => 'Estado cambiado correctamente'], 200);
    }
}
