<?php

namespace App\Http\Controllers;

use App\Models\FarmaciasModelo;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;

class FarmaciaController extends Controller
{
    //

    public function getListFarmacia(Request $request)
    {
        if ($request->id != '') {
            $query = FarmaciasModelo::where('id_farmacia', $request->id)->first();
        } else {
            $query = FarmaciasModelo::get();
        }
        return response()->json($query, 200);
    }

    public function getListFarmaciaCuit($cuit)
    {
        $query = FarmaciasModelo::where('cuit', $cuit)->first();
        return response()->json($query, 200);
    }

    public function getLikeFarmacia($dato)
    {
        $query = FarmaciasModelo::where('cuit', 'LIKE', "$dato%")
            ->orWhere('razon_social', 'LIKE', "$dato%")->get();
        return response()->json($query, 200);
    }

    public function getFechaFarmacia(Request $request)
    {
        $query = FarmaciasModelo::whereBetween('fecha_alta', [$request->desde, $request->hasta])->get();
        return response()->json($query, 200);
    }

    public function postSaveFarmacia(Request $request)
    {

        if ($request->id_farmacia != '') {
            $query = FarmaciasModelo::where('id_farmacia', $request->id_farmacia)->first();
            $query->fecha_alta = $request->fecha_alta;
            $query->fecha_baja = $request->fecha_baja;
            $query->activo = $request->activo;
            $query->id_usuario = $request->id_usuario;
            $query->cuit = $request->cuit;
            $query->razon_social = $request->razon_social;
            $query->domicilio = $request->domicilio;
            $query->representante = $request->representante;
            $query->id_localidad = $request->id_localidad;
            $query->id_partido = $request->id_partido;
            $query->id_provincia = $request->id_provincia;
            $query->observaciones = $request->observaciones;
            $query->nombre_fantasia = $request->nombre_fantasia;
            $query->save();
            $msg = 'datos de farmacia actualizado correctamente';
        } else {
            $farmacia = FarmaciasModelo::where('cuit', $request->cuit)->first();
            if ($farmacia) {
                return response()->json(['message' => 'la farmacia ya se encuentra registrado'], 500);
            }
            $user = Auth::user();
            FarmaciasModelo::create([
                'fecha_alta' => $request->fecha_alta,
                'fecha_baja' => $request->fecha_baja,
                'activo' => $request->activo,
                'id_usuario' => $user->cod_usuario,
                'cuit' => $request->cuit,
                'razon_social' => $request->razon_social,
                'domicilio' => $request->domicilio,
                'representante' => $request->representante,
                'id_localidad' => $request->id_localidad,
                'id_partido' => $request->id_partido,
                'id_provincia' => $request->id_provincia,
                'observaciones' => $request->observaciones,
                'nombre_fantasia' => $request->nombre_fantasia,
            ]);
            $msg = 'datos de farmacia registrados correctamente';
        }
        return response()->json(['message' => $msg], 200);
    }

    public function postdeleteFarmacia(Request $request){
        FarmaciasModelo::where('id_farmacia', $request->id_farmacia)->delete();
        return response()->json(['message' => 'Farmacia eliminado correctamente'], 200);
    }
}
