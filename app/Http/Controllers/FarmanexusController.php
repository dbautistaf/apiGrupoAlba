<?php

namespace App\Http\Controllers;

use App\Imports\FarmanexusImport;
use App\Models\FarmanexusModelo;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class FarmanexusController extends Controller
{
    //
    public function getFarmanexus(Request $request)
    {
        if ($request->datos == '') {
            $datos =  FarmanexusModelo::get();
        } else {
            $datos = FarmanexusModelo::where('afiliado', 'LIKE', "$request->datos%")
            ->orWhere('razon_social', 'LIKE', "$request->datos%")->get();
        }
        return response()->json($datos, 200);
    }

    public function getFechaFarmanexus(Request $request)
    {
        $query = FarmanexusModelo::whereBetween('fecha_proceso', [$request->desde, $request->hasta])->get();
        return response()->json($query, 200);
    }

    public function saveFarmanexus(Request $request)
    {
        $archivo = $request->file('file');
        if ($archivo) {
            try {
                $user = Auth::user();
                DB::beginTransaction();
                $importacion = new FarmanexusImport($request->fecha_proceso, $user->cod_usuario);
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
