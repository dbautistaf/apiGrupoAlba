<?php

namespace App\Http\Controllers;

use App\Models\EscolaridadModelo;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class EscolaridadController extends Controller
{
    //
    public function getEscolaridad($idPadron)
    {
        $escolaridad =  EscolaridadModelo::with(['afiliado', 'tipo'])->where('id_padron', $idPadron)->first();
        return response()->json($escolaridad, 200);
    }

    public function saveEscolaridad(Request $request)
    {
        $user = Auth::user();
        $nombre_archivo = null;
        $fecha=Carbon::now('America/Argentina/Buenos_Aires');
        //$anioActual = Carbon::now('America/Argentina/Buenos_Aires')->year;
        //$horaCarga = Carbon::now('America/Argentina/Buenos_Aires')->format('H-i-s');
        $model = json_decode($request->data);
        if ($request->hasFile('archivo')) {
            $fileStorage = $request->file('archivo');
            $nombre_archivo = 'ESCOLARIDAD_' . $model->id_padron . "_" . $fecha->format('H-i-s'). "_AF_" . $fecha->year. "." . $fileStorage->extension();
            $destinationPath = "public/escolaridad";
            Storage::putFileAs($destinationPath, $fileStorage, $nombre_archivo);
        }
        if ($request->id != '') {
            $query = EscolaridadModelo::where('id', $request->id)->first();
            $query->nivel_estudio = $request->nivel_estudio;
            $query->fecha_presentacion = $request->fecha_presentacion;
            $query->fecha_vencimiento = $request->fecha_vencimiento;
            $query->id_padron = $request->id_padron;
            $query->save();
            $msg = 'Datos actualizados correctamente';
        } else {
            $escolaridad =  EscolaridadModelo::where('id_padron', $request->id_padron)->first();
            if ($escolaridad) {
                return response()->json(['message' => 'El afiliado ya tiene un registro de escolaridad'], 500);
            } else {
                EscolaridadModelo::create([
                    'nivel_estudio' => $model->nivel_estudio,
                    'fecha_presentacion' => $model->fecha_presentacion,
                    'fecha_vencimiento' => $model->fecha_vencimiento,
                    'id_padron' => $model->id_padron,
                    'url_adjunto' => $nombre_archivo,
                    'fecha_registra' =>$fecha,
                    'cod_usuario_registra' => $user->cod_usuario,
                ]);
                $msg = 'Datos de Escolaridad registrado correctamente';
            }
        }
        return response()->json(['message' => $msg], 200);
    }
}
