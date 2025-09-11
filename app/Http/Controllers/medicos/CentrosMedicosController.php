<?php

namespace App\Http\Controllers\medicos;

use App\Models\medicos\CentrosMedicosEntity;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class CentrosMedicosController extends Controller
{
    public function getListCentroMedico(Request $request)
    {
        if ($request->id != '') {
            $query = CentrosMedicosEntity::where('id_centro_medico', $request->id)->first();
        } else {
            $query = CentrosMedicosEntity::get();
        }
        return response()->json($query, 200);
    }

    public function getLikeEntrega($dato)
    {
        $query = CentrosMedicosEntity::where('nombre', 'LIKE', "$dato%")
        ->orWhere('responsable', 'LIKE', "$dato%")->get();
        return response()->json($query, 200);
    }

    public function saveCentroMedico(Request $request)
    {
        if ($request->id_centro_medico != '') {
            $query = CentrosMedicosEntity::where('id_centro_medico', $request->id_centro_medico)->first();
            $query->nombre=$request->nombre;
            $query->fecha_alta=$request->fecha_alta;
            $query->fecha_baja=$request->fecha_baja;
            $query->observaciones=$request->observaciones;
            $query->responsable=$request->responsable;
            $query->email=$request->email;
            $query->celular=$request->celular;
            $query->telefono=$request->telefono;
            $query->activo=$request->activo;
            $query->save();
            $msg = 'Centro Medico actualizado correctamente';
        } else {
            CentrosMedicosEntity::create([
                'nombre'=>$request->nombre,
                'fecha_alta'=>$request->fecha_alta,
                'fecha_baja'=>$request->fecha_baja,
                'observaciones'=>$request->observaciones,
                'responsable'=>$request->responsable,
                'email'=>$request->email,
                'celular'=>$request->celular,
                'telefono'=>$request->telefono,
                'activo'=>$request->activo
            ]);
            $msg = 'Centro medico registrado correctamente';
        }
        return response()->json(['message' => $msg], 200);
    }

    public function deleteCentroMedico(Request $request)
    {
        CentrosMedicosEntity::where('id_centro_medico', $request->id_centro_medico)->delete();
        return response()->json(['message' => 'Centro Medico eliminado correctamente'], 200);
    }
}
