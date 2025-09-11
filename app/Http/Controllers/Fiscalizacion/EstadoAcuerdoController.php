<?php

namespace App\Http\Controllers\Fiscalizacion;

use App\Models\Fiscalizacion\EstadoAcuerdo;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class EstadoAcuerdoController extends Controller
{
    public function postSaveEstadoAcuerdo(Request $request)
    {
        $msg = '';

        if ($request->id_estado_acuerdo == '') {
            // Crear un nuevo estado de acuerdo
            EstadoAcuerdo::create([
                'descripcion' => $request->descripcion,
            ]);

            $msg = 'Estado de acuerdo registrado correctamente';
        } else {
            // Actualizar un estado de acuerdo existente
            $estadoAcuerdo = EstadoAcuerdo::find($request->id_estado_acuerdo);
            $estadoAcuerdo->update([
                'descripcion' => $request->descripcion,
            ]);

            $msg = 'Estado de acuerdo actualizado correctamente';
        }

        return response()->json(['message' => $msg], 200);
    }

    public function getListEstadosAcuerdo()
    {
        $query = EstadoAcuerdo::all();
        return response()->json($query, 200);
    }

    public function getEstadoAcuerdoById($id)
    {
        $query = EstadoAcuerdo::find($id);
        return response()->json($query, 200);
    }
}