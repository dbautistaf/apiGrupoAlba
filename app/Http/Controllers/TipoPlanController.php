<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use App\Models\afiliado\AfiliadoTipoPlanEntity;
use Illuminate\Support\Facades\Auth;

class TipoPlanController extends Controller
{

    public function getPlan()
    {
        $query =  AfiliadoTipoPlanEntity::where('activo', 1)->get();
        return response()->json($query, 200);
    }

    public function getPlanGeneral()
    {
        $query =  AfiliadoTipoPlanEntity::get();
        return response()->json($query, 200);
    }

    public function filterPlan($id)
    {
        return AfiliadoTipoPlanEntity::where('id_tipo_plan', $id)->first();
    }

    public function savePlan(Request $request)
    {
        $user = Auth::user();
        if ($request->id_tipo_plan != '') {
            $query = AfiliadoTipoPlanEntity::where('id_tipo_plan', $request->id_tipo_plan)->first();
            $query->tipo = $request->tipo;
            $query->activo = $request->activo;
            $query->id_usuario = $user->cod_usuario;
            $query->observaciones = $request->observaciones;
            $query->save();
            return response()->json(['message' => 'Plan actualizado correctamente'], 200);
        } else {
            AfiliadoTipoPlanEntity::create([
                'id_tipo_plan' => $request->id_tipo_plan,
                'tipo' => $request->tipo,
                'activo' => 1,
                'id_usuario' => $user->cod_usuario,
                'observaciones' => $request->observaciones,
            ]);
            return response()->json(['message' => 'Plan registrados correctamente'], 200);
        }
    }

    public function updateEstado(Request $request)
    {
        AfiliadoTipoPlanEntity::where('id_tipo_plan', $request->id)->update(['activo' => $request->activo,]);
        return response()->json(['message' => 'Estado cambiado correctamente'], 200);
    }
}
