<?php

namespace App\Http\Controllers\medicos;

use App\Models\medicos\EspecialidadesMedicasEntity;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class EspecialidadController extends Controller
{
    //
    public function getListEspecialidad(Request $request)
    {
        if ($request->id != '') {
            $query = EspecialidadesMedicasEntity::with('centromedico')->where('id_especialidad', $request->id)->first();
        } else {
            $query = EspecialidadesMedicasEntity::with('centromedico')->get();
        }
        return response()->json($query, 200);
    }

    public function getLikeEspecialidad($dato)
    {
        $query = EspecialidadesMedicasEntity::where('nombre', 'LIKE', "$dato%")->get();
        return response()->json($query, 200);
    }

    public function saveEspecialidad(Request $request)
    {
        if ($request->id_especialidad != '') {
            $query = EspecialidadesMedicasEntity::where('id_especialidad', $request->id_especialidad)->first();
            $query->especialidad=$request->especialidad;
            $query->intervalo=$request->intervalo;
            $query->activo=$request->activo;
            $query->id_centro_medico=$request->id_centro_medico;
            $query->save();
            $msg = 'Especialidad actualizado correctamente';
        } else {
            EspecialidadesMedicasEntity::create([
                'especialidad'=>$request->especialidad,
                'intervalo'=>$request->intervalo,
                'activo'=>$request->activo,
                'id_centro_medico'=>$request->id_centro_medico
            ]);
            $msg = 'Especialidad registrado correctamente';
        }
        return response()->json(['message' => $msg], 200);
    }

    public function deleteEspecialidad(Request $request)
    {
        EspecialidadesMedicasEntity::where('id_especialidad', $request->id_especialidad)->delete();
        return response()->json(['message' => 'Especialidad eliminado correctamente'], 200);
    }

    public function updateEstado(Request $request)
    {
        EspecialidadesMedicasEntity::where('id_especialidad', $request->id_especialidad)->update(['activo' => $request->id_estado]);
        return response()->json(['message' => 'Estado actualizado correctamente'], 200);
    }
}
