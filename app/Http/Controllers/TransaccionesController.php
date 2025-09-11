<?php

namespace App\Http\Controllers;

use App\Imports\TransaccionesImport;
use App\Models\TransaccionesModel;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class TransaccionesController extends Controller
{
    public function getTransacciones(Request $request)
    {
        if ($request->datos == '') {
            $datos =  TransaccionesModel::get();
        } else {
            $datos = TransaccionesModel::where('nombre_afiliado', 'LIKE', "$request->datos%")
            ->orWhere('nro_receta', 'LIKE', "$request->datos%")
            ->orWhere('id_autorizacion', 'LIKE', "$request->datos%")
            ->orWhere('cuil', 'LIKE', "$request->datos%")
            ->orWhere('nombre_farmacia', 'LIKE', "$request->datos%")
            ->orWhere('cuit', 'LIKE', "$request->datos%")->get();
        }
        return response()->json($datos, 200);
    }

    public function getTransaccionesNumReceta(Request $request)
    {
        if ($request->datos == '') {
            $datos =  TransaccionesModel::get();
        } else {
            $datos = TransaccionesModel::with(['afiliado','farmacia','detalles'])->where('nro_receta', $request->datos)->first();
        }
        return response()->json($datos, 200);
    }


    public function getFechaTransacciones(Request $request)
    {
        $query = TransaccionesModel::whereBetween('fecha_carga', [$request->desde, $request->hasta])->get();
        return response()->json($query, 200);
    }

    public function saveTransacciones(Request $request)
    {
        $archivo = $request->file('file');
        if ($archivo) {
            try {
                $user = Auth::user();
                DB::beginTransaction();
                $importacion = new TransaccionesImport($request->fecha_proceso, $user->cod_usuario);
                Excel::import($importacion, $archivo);
                DB::commit();
                $mensaje = $importacion->getMensaje();
                return response()->json(['message' => $mensaje], 200);
            } catch (\Throwable $exception) {
                DB::rollBack();
                return response()->json(['message' => $exception->getMessage()], 500);
            }
        } else {
            return response()->json(['message' => 'No se encontro ningun archivo'], 500);
        }
    }
}
