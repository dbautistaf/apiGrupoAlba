<?php

namespace App\Http\Controllers;

use App\Models\EntregaModelos;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;

class EntregaController extends Controller
{
    //

    public function getListEntregas(Request $request)
    {
        if ($request->id != '') {
            $query = EntregaModelos::where('id_entrega', $request->id)->first();
        } else {
            $query = EntregaModelos::get();
        }
        return response()->json($query, 200);
    }

    public function getLikeEntrega($dato)
    {
        $query = EntregaModelos::where('personal_recibe', 'LIKE', "$dato%")->get();
        return response()->json($query, 200);
    }

    public function getFechaEntrega(Request $request)
    {
        $query = EntregaModelos::whereBetween('fecha_entrega', [$request->desde, $request->hasta])->get();
        return response()->json($query, 200);
    }

    public function saveEntrega(Request $request)
    {
        if ($request->id_entrega != '') {
            $query = EntregaModelos::where('id', $request->id_entrega)->first();
            $query->num_caja = $request->num_caja;
            $query->fecha_entrega = $request->fecha_entrega;
            $query->observaciones = $request->observaciones;
            $query->id_usuario = $request->id_usuario;
            $query->personal_recibe = $request->personal_recibe;
            $query->save();
            $msg = 'Entrega actualizado correctamente';
        } else {
            $user = Auth::user();
            EntregaModelos::create([
                'id_entrega' => $request->id_entrega,
                'num_caja' => $request->num_caja,
                'fecha_entrega' => $request->fecha_entrega,
                'observaciones' => $request->observaciones,
                'id_usuario' => $user->cod_usuario,
                'personal_recibe' => $request->personal_recibe,
            ]);
            $msg = 'Entrega registrado correctamente';
        }
        return response()->json(['message' => $msg], 200);
    }
}
