<?php

namespace App\Http\Controllers\Fiscalizacion;

use App\Models\Fiscalizacion\CobranzaPeriodo;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use App\Models\Fiscalizacion\Expediente;

class CobranzaPeriodoController extends Controller
{
    public function postSaveCobranzaPeriodo(Request $request)
    {
        $msg = '';

        if ($request->id_cobranza_periodo == '') {
            // Crear un nuevo período de cobranza
            CobranzaPeriodo::create([
                'id_cobranza' => $request->id_cobranza,
                'id_periodo' => $request->id_periodo,
                'monto_asociado' => $request->monto_asociado,
            ]);

            $msg = 'Período de cobranza registrado correctamente';
        } else {
            // Actualizar un período de cobranza existente
            $cobranzaPeriodo = CobranzaPeriodo::find($request->id_cobranza_periodo);
            $cobranzaPeriodo->update([
                'id_cobranza' => $request->id_cobranza,
                'id_periodo' => $request->id_periodo,
                'monto_asociado' => $request->monto_asociado,
            ]);

            $msg = 'Período de cobranza actualizado correctamente';
        }

        return response()->json(['message' => $msg], 200);
    }

    public function getListCobranzasPeriodo()
    {
        $query = CobranzaPeriodo::with('cobranza')->with('periodo')->get();
        return response()->json($query, 200);
    }

    public function getCobranzaPeriodoById($id)
    {
        $query = CobranzaPeriodo::with('cobranza')->with('periodo')->find($id);
        return response()->json($query, 200);
    }

    
}