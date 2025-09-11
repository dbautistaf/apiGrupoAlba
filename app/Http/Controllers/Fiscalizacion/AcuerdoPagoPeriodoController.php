<?php

namespace App\Http\Controllers\Fiscalizacion;

use App\Models\Fiscalizacion\AcuerdoPagoPeriodo;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;

class AcuerdoPagoPeriodoController extends Controller
{
    public function postSaveAcuerdoPagoPeriodo(Request $request)
    {
        $msg = '';
        $user = Auth::user();

        if ($request->id_acuerdo_pago_periodo == '') {
            // Crear un nuevo período de acuerdo de pago
            AcuerdoPagoPeriodo::create([
                'id_acuerdo_pago' => $request->id_acuerdo_pago,
                'id_periodo' => $request->id_periodo,
                'monto_asociado' => $request->monto_asociado,
                'usuario' => $user->nombre_apellidos,
            ]);

            $msg = 'Período de acuerdo de pago registrado correctamente';
        } else {
            // Actualizar un período de acuerdo de pago existente
            $acuerdoPagoPeriodo = AcuerdoPagoPeriodo::find($request->id_acuerdo_pago_periodo);
            $acuerdoPagoPeriodo->update([
                'id_acuerdo_pago' => $request->id_acuerdo_pago,
                'id_periodo' => $request->id_periodo,
                'monto_asociado' => $request->monto_asociado,
            ]);

            $msg = 'Período de acuerdo de pago actualizado correctamente';
        }

        return response()->json(['message' => $msg], 200);
    }

    public function getListAcuerdosPagoPeriodo()
    {
        $query = AcuerdoPagoPeriodo::with('acuerdoPago')->with('periodo')->get();
        return response()->json($query, 200);
    }

    public function getAcuerdoPagoPeriodoById($id)
    {
        $query = AcuerdoPagoPeriodo::with('acuerdoPago')->with('periodo')->find($id);
        return response()->json($query, 200);
    }
}