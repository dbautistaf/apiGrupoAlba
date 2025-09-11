<?php

namespace App\Http\Controllers\PrestacionesMedicas\Services;

use App\Http\Controllers\PrestacionesMedicas\Repository\SolicitudLentesFilterRepository;
use App\Models\PrestacionesMedicas\DiagnosticoEntity;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;

class DiagnosticoController extends Controller
{


    public function getListarData()
    {
        return DiagnosticoEntity::where('estado', '1')->get();
    }

    public function postSaveDiagnostico(Request $request)
    {
        $user = Auth::user();
        $now = Carbon::now('America/Argentina/Buenos_Aires');
        if ($request->id_cartilla != '') {
            $query = DiagnosticoEntity::where('id_diagnostico', $request->id_diagnostico)->first();
            if ($query) {
                $query->descripcion = $request->descripcion;
                $query->estado = $request->estado;
                $query->fecha_registra = $request->fecha_registra;
                $query->cod_usuario = $request->cod_usuario;
                $query->save();
                return response()->json(['message' => 'Diagnostico actualizado correctamente'], 200);
            }
        } else {
            $result = DiagnosticoEntity::where('descripcion', $request->descripcion)->first();
            if ($result) {
                return response()->json(['message' => 'Diagnostico ya existe'], 500);
            }
            DiagnosticoEntity::create([
                'descripcion' => $request->descripcion,
                'estado' => 1,
                'fecha_registra' => $now->format('Y-m-d'),
                'cod_usuario' => $user->cod_usuario,
            ]);
            return response()->json(['message' => 'Diagnostico registrado correctamente'], 200);
        }
    }
}
