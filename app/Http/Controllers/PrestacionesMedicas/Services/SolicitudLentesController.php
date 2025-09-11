<?php

namespace App\Http\Controllers\PrestacionesMedicas\Services;

use App\Http\Controllers\PrestacionesMedicas\Repository\SolicitudLentesRepository;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;

class SolicitudLentesController extends Controller
{

    public function getProcesarSolicitud(SolicitudLentesRepository $repo, Request $request)
    {
        DB::beginTransaction();
        try {
            if (!is_null($request->id_solitud_lente)) {
                $repo->findByUpdate($request);
                DB::commit();
                return response()->json(["message" => 'Solicitud actualizada correctamente']);
            } else {
                $repo->findByCreate($request);
                DB::commit();
                return response()->json(["message" => 'Solicitud procesada correctamente']);
            }
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'code' => $th->getCode(),
                'message' => $th->getMessage()
            ], 500);
        }
    }

    public function getAutorizarEntrega(SolicitudLentesRepository $repo, Request $request)
    {
        DB::beginTransaction();
        try {
            $repo->findByUpdateEntrega($request->id, $request->obs);
            $repo->findByUpdateEstado($request->id, 4);
            DB::commit();
            return response()->json(["message" => 'Entrega registrada correctamente']);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'code' => $th->getCode(),
                'message' => $th->getMessage()
            ], 500);
        }
    }
}
