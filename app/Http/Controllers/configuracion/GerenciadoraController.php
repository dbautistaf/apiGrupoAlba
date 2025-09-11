<?php

namespace App\Http\Controllers\configuracion;

use App\Http\Controllers\Controller;
use App\Models\configuracion\Gerenciadora;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as RoutingController;

class GerenciadoraController extends RoutingController
{
    //
    public function getListaGerenciadora()
    {
        return Gerenciadora::get();
    }

    public function saveGerenciadora(Request $request)
    {
        if ($request->id_gerenciadora) {
            $query = Gerenciadora::where('id_gerenciadora', $request->id_gerenciadora)->first();
            $query->detalle_gerenciadora = $request->detalle_gerenciadora;
            $query->save();
            return response()->json(['message' => 'Datos de Gerenciadora actualizado correctamente'], 200);
        } else {
            Gerenciadora::create([
                'detalle_gerenciadora' => $request->detalle_gerenciadora,
                'estado' => 1
            ]);
            return response()->json(['message' => 'Datos de Gerenciadora registrados correctamente'], 200);
        }
    }

    public function updateEstado(Request $request)
    {
        Gerenciadora::where('id_gerenciadora', $request->id)->update(['estado' => $request->activo,]);
        return response()->json(['message' => 'Estado cambiado correctamente'], 200);
    }

    public function getIdGerenciadora($id)
    {
        return Gerenciadora::where('id_gerenciadora', $id)->first();
    }
}
