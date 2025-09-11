<?php

namespace App\Http\Controllers\Fiscalizacion;

use App\Models\Fiscalizacion\TipoMovimiento;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class MovimientoController extends Controller
{
    public function postSaveMovimiento(Request $request)
    {
        $msg = '';

        if ($request->id_movimiento == '') {
            // Crear un nuevo movimiento
            Movimiento::create([
                'descripcion' => $request->descripcion,
            ]);

            $msg = 'Movimiento registrado correctamente';
        } else {
            // Actualizar un movimiento existente
            $movimiento = Movimiento::find($request->id_movimiento);
            $movimiento->update([
                'descripcion' => $request->descripcion,
            ]);

            $msg = 'Movimiento actualizado correctamente';
        }

        return response()->json(['message' => $msg], 200);
    }

    public function getListMovimientos()
    {
        $query = TipoMovimiento::all();
        return response()->json($query, 200);
    }

    public function getMovimientoById($id)
    {
        $query = TipoMovimiento::find($id);
        return response()->json($query, 200);
    }
}