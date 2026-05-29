<?php

namespace App\Http\Controllers\Internaciones\Services;

use App\Http\Controllers\Internaciones\Repository\AutorizacionDatosRNRepository;
use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AutorizacionDatosRNController extends Controller
{
    public function getConsultarAutorizaciones(Request $request, AutorizacionDatosRNRepository $repo)
    {
        try {
            $data = $repo->findByList($request);
            return response()->json($data, 200);
        } catch (\Throwable $th) {
            return response()->json([
                'code' => $th->getCode(),
                'message' => $th->getMessage()
            ], 500);
        }
    }

    public function getObtenerAutorizacion(Request $request, AutorizacionDatosRNRepository $repo)
    {
        try {
            $id = $request->id ?? $request->cod_prestacion_rn;
            if (empty($id)) {
                return response()->json(['message' => 'El código de prestación es requerido'], 400);
            }
            $data = $repo->findById($id);
            return response()->json($data, 200);
        } catch (\Throwable $th) {
            return response()->json([
                'code' => $th->getCode(),
                'message' => $th->getMessage()
            ], 500);
        }
    }

    public function postGuardarAutorizacion(Request $request, AutorizacionDatosRNRepository $repo)
    {
        try {
            DB::beginTransaction();
            $message = "Autorización de recién nacido registrada correctamente.";
            
            if (!empty($request->cod_prestacion_rn)) {
                $repo->findByUpdate($request->cod_prestacion_rn, $request);
                $message = "Autorización de recién nacido actualizada correctamente.";
            } else {
                $repo->findBySave($request);
            }
            
            DB::commit();
            return response()->json(["message" => $message], 200);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'code' => $th->getCode(),
                'message' => $th->getMessage()
            ], 500);
        }
    }

    public function deleteEliminarAutorizacion(Request $request, AutorizacionDatosRNRepository $repo)
    {
        try {
            $id = $request->id ?? $request->cod_prestacion_rn;
            if (empty($id)) {
                return response()->json(['message' => 'El código de prestación es requerido'], 400);
            }
            
            DB::beginTransaction();
            $repo->findByDeleteId($id);
            DB::commit();
            
            return response()->json(['message' => 'Autorización de recién nacido eliminada correctamente'], 200);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'code' => $th->getCode(),
                'message' => $th->getMessage()
            ], 500);
        }
    }
}
