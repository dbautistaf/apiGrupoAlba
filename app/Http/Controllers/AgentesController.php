<?php

namespace App\Http\Controllers;

use App\Models\AgentesModelo;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class AgentesController extends Controller
{
    //
    public function getAgentesActivos()
    {
        $agentes =  AgentesModelo::where('activo', '1')->get();
        return response()->json($agentes, 200);
    }

    public function getAgentes()
    {
        $agentes =  AgentesModelo::get();
        return response()->json($agentes, 200);
    }

    public function filterAgente($id)
    {
        return AgentesModelo::where('id_agente', $id)->first();
    }

    public function saveAgente(Request $request)
    {

        if ($request->id_agente != '') {
            $query = AgentesModelo::where('id_agente', $request->id_agente)->first();
            $query->nombres_agente = $request->nombres_agente;
            $query->activo = $request->activo;
            $query->save();
            return response()->json(['message' => 'Datos de agente actualizado correctamente'], 200);
        } else {
            AgentesModelo::create([
                'id_agente' => $request->id_agente,
                'nombres_agente' => $request->nombres_agente,
                'activo' => $request->activo,
            ]);
            return response()->json(['message' => 'Datos de agente registrados correctamente'], 200);
        }
    }

    public function updateEstado(Request $request)
    {
        AgentesModelo::where('id_agente', $request->id)->update(['activo' => $request->activo,]);
        return response()->json(['message' => 'Estado cambiado correctamente'], 200);
    }
}
