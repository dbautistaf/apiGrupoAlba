<?php

namespace App\Http\Controllers;

use App\Models\CronicosModelo;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;

class CronicosController extends Controller
{
    //
    public function getListCronicos(Request $request)
    {
        if ($request->id != '') {
            $query = CronicosModelo::where('id_padron', $request->id)->first();
        } else {
            $query = CronicosModelo::get();
        }
        return response()->json($query, 200);
    }

    public function postSaveCronicos(Request $request)
    {
        $now = new \DateTime();
        if ($request->id_cronico != '') {
            $query = CronicosModelo::where('id_cronico', $request->id_cronico)->first();
            $query->id_patologia = $request->id_patologia;
            $query->observaciones = $request->observaciones;
            $query->fecha_alta = $request->fecha_alta;
            $query->fecha_baja = $request->fecha_baja;
            $query->fecha_carga = $request->fecha_carga;
            $query->id_usuario = $request->id_usuario;
            $query->id_padron = $request->id_padron;
            $query->save();
            $msg = 'datos de afiliado cronico actualizado correctamente';
        } else {
            $cronicos = CronicosModelo::where('id_padron', $request->id_padron)->first();
            if ($cronicos) {
                return response()->json(['message' => 'El Afiliado ya se encuentra registrado como afiliado cronico'], 500);
            }
            $user = Auth::user();
            CronicosModelo::create([
                'id_patologia' => $request->id_patologia,
                'observaciones' => $request->observaciones,
                'fecha_alta' => $request->fecha_alta,
                'fecha_baja' => $request->fecha_baja,
                'fecha_carga' => $now->format('Y-m-d'),
                'id_usuario' => $user->cod_usuario,
                'id_padron' => $request->id_padron
            ]);
            $msg = 'datos de afiliado cronico registrados correctamente';
        }
        return response()->json(['message' => $msg], 200);
    }
}
