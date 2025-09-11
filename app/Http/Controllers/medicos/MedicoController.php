<?php

namespace App\Http\Controllers\medicos;

use App\Models\medicos\MedicosEntity;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class MedicoController extends Controller
{
    public function getListMedico(Request $request)
    {
        if ($request->id != '') {
            $query = MedicosEntity::with('especialidad')->where('id_medico', $request->id)->first();
        } else {
            $query = MedicosEntity::with('especialidad')->get();
        }
        return response()->json($query, 200);
    }

    public function getLikeMedico($dato)
    {
        $query = MedicosEntity::with('especialidad')->where('nombre', 'LIKE', "$dato%")
            ->orWhere('cuit', 'LIKE', "$dato%")
            ->orWhere('matricula_nacional', 'LIKE', "$dato%")
            ->orWhere('matricula_provincial', 'LIKE', "$dato%")->get();
        return response()->json($query, 200);
    }

    public function saveMedico(Request $request)
    {
        if ($request->id_medico != '') {
            $query = MedicosEntity::where('id_medico', $request->id_medico)->first();
            $query->universidad = $request->universidad;
            $query->nombre = $request->nombre;
            $query->cuit = $request->cuit;
            $query->matricula_nacional = $request->matricula_nacional;
            $query->matricula_provincial = $request->matricula_provincial;
            $query->tipo_matricula = $request->tipo_matricula;
            $query->fecha_alta = $request->fecha_alta;
            $query->fecha_baja = $request->fecha_baja;
            $query->email = $request->email;
            $query->celular = $request->celular;
            $query->observaciones = $request->observaciones;
            $query->activo = $request->activo;
            $query->id_especialidad = $request->id_especialidad;
            $query->id_tipo_entidad = $request->id_tipo_entidad;
            $query->save();
            $msg = 'Especialidad actualizado correctamente';
        } else {
            MedicosEntity::create([
                'universidad' => $request->universidad,
                'nombre' => $request->nombre,
                'cuit' => $request->cuit,
                'matricula_nacional' => $request->matricula_nacional,
                'matricula_provincial' => $request->matricula_provincial,
                'tipo_matricula' => $request->tipo_matricula,
                'fecha_alta' => $request->fecha_alta,
                'fecha_baja' => $request->fecha_baja,
                'email' => $request->email,
                'celular' => $request->celular,
                'observaciones' => $request->observaciones,
                'activo' => $request->activo,
                'id_especialidad' => $request->id_especialidad,
                'id_tipo_entidad' => $request->id_tipo_entidad,
            ]);
            $msg = 'Centro medico registrado correctamente';
        }
        return response()->json(['message' => $msg], 200);
    }

    public function deleteMedico(Request $request)
    {
        MedicosEntity::where('id_especialidad', $request->id_especialidad)->delete();
        return response()->json(['message' => 'Especialidad eliminado correctamente'], 200);
    }
}
