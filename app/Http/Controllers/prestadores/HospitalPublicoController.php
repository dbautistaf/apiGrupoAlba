<?php

namespace App\Http\Controllers\prestadores;

use App\Http\Controllers\Controller;
use App\Models\prestadores\HospitalPublicoEntity;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as RoutingController;

class HospitalPublicoController extends RoutingController
{
    //
    public function getListaHospital(Request $request)
    {
        if($request->cuit==''){
            return HospitalPublicoEntity::with('provincia')->get();
        }else{
            return HospitalPublicoEntity::with('provincia')->where('cuit', 'LIKE', "$request->cuit%")
            ->orWhere('nombre', 'LIKE', "$request->cuit%")->get();
        }
        
    }

    public function saveHospital(Request $request)
    {
        if ($request->id_hospital) {
            $query = HospitalPublicoEntity::where('id_hospital', $request->id_hospital)->first();
            $query->cuit = $request->cuit;
            $query->nombre = $request->nombre;
            $query->domicilio = $request->domicilio;
            $query->cod_provincia = $request->cod_provincia;
            $query->telefono = $request->telefono;
            $query->fecha_alta= $request->fecha_alta;
            $query->save();
            return response()->json(['message' => 'Datos del Hospital Público actualizado correctamente'], 200);
        } else {
            HospitalPublicoEntity::create([
                'cuit' => $request->cuit,
                'nombre' => $request->nombre,
                'domicilio' => $request->domicilio,
                'cod_provincia' => $request->cod_provincia,
                'telefono' => $request->telefono,
                'fecha_alta'=>$request->fecha_alta,
            ]);
            return response()->json(['message' => 'Datos del Hospital Público registrados correctamente'], 200);
        }
    }

    public function getIdHospital($id)
    {
        return HospitalPublicoEntity::where('id_hospital', $id)->first();
    }
}
