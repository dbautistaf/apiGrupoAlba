<?php

namespace App\Http\Controllers\Reclamos;

use App\Http\Controllers\Controller;
use App\Models\Reclamos\ReclamosModel;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as RoutingController;
use Illuminate\Support\Facades\Auth;

class ReclamosController extends RoutingController
{
    //
    public function getReclamosUser()
    {
        $user = Auth::user();
        $query = ReclamosModel::where('dni_afiliado', $user->documento)->get();
        return response()->json($query, 200);
    }

    public function getReclamos(Request $request)
    {
        $query = ReclamosModel::with(['padron', 'tiporeclamo']);
        if (!empty($request->id_tipo_reclamo)) {
            $query = $query->where('id_tipo_reclamo', '=', $request->id_tipo_reclamo);
        }

        if (!empty($request->estado)) {
            $query = $query->where('estado_reclamo', '=', $request->estado);
        }
        $reclamos = $query->orderBy('fecha_reclamo', 'desc')->get();
        return response()->json($reclamos, 200);
    }

    public function getIdReclamo($id)
    {
        $query = ReclamosModel::with('padron')->where('id_reclamo', $id)->first();
        return response()->json($query, 200);
    }

    public function postSaveReclamos(Request $request)
    {
        $now = new \DateTime('now', new \DateTimeZone('America/Argentina/Buenos_Aires'));
        if ($request->id_reclamo != '') {

            $query = ReclamosModel::where('id_reclamo', $request->id_reclamo)->first();
            if ($query) {
                $query->dni_afiliado=$request->dni_afiliado;
                $query->fecha_Reclamo=$request->fecha_Reclamo;
                $query->tipo_reclamo=$request->tipo_reclamo;
                $query->detalle_Reclamo=$request->detalle_Reclamo;
                $query->detalle_respuesta=$request->detalle_respuesta;
                $query->estado_reclamo=$request->estado_reclamo;
                $query->fecha_respuesta=$now->format('Y-m-d H:i:s');
                $query->id_usuario=$request->id_usuario;
                $query->save();

                return response()->json(['message' => 'El reclamo fue respondido correctamente'], 200);
            }
        } else {
            $user = Auth::user();
            ReclamosModel::create([
                'dni_afiliado'=>$user->documento,
                'fecha_Reclamo'=>$now->format('Y-m-d H:i:s'),
                'tipo_reclamo'=>$request->tipo_reclamo,
                'detalle_Reclamo'=>$request->detalle_Reclamo,
                'detalle_respuesta'=>$request->detalle_respuesta,
                'estado_reclamo'=>$request->estado_reclamo,
                'fecha_respuesta'=>$request->fecha_respuesta,
                'id_usuario'=>$user->cod_usuario,
            ]);
            return response()->json(['message' => 'Su reclamo fue registrado correctamente'], 200);
        }
    }
}
