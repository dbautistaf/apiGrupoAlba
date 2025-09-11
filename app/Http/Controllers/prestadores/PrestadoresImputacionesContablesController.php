<?php

namespace App\Http\Controllers\prestadores;

use App\Http\Controllers\prestadores\repository\ImputacionesPrestadoreRepository;
use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PrestadoresImputacionesContablesController extends Controller
{

    public function getProcesar(Request $request, ImputacionesPrestadoreRepository $repo)
    {
        if (!is_null($request->id_imputacion_prestador)) {
            $repo->findByUpdateImputaciones($request,'');
        } else {
            $repo->findByAgregarImputaciones($request,'');
        }
        return response()->json(["message" => "Registro procesado correctamente"], 200);
    }

    public function getListar(Request $request, ImputacionesPrestadoreRepository $repo)
    {
        return response()->json($repo->findByListImputaciones($request->codPrestador), 200);
    }

    public function getAnular(Request $request, ImputacionesPrestadoreRepository $repo)
    {
        $repo->findByAnularImputaciones($request);
        return response()->json(["message" => "Registro eliminado correctamente"], 200);
    }

    public function getProcesarTipo(Request $request, ImputacionesPrestadoreRepository $repo)
    {
        DB::beginTransaction();
        try {
            if (!is_null($request->id_tipo_imputacion_contable)) {

                $repo->findByUpdateTipoImputacion($request);
            } else {
                if ($repo->findByExistsTipoImputacion($request->codigo)) {
                    DB::rollBack();
                    return response()->json(["message" => "El codigo de imputación $request->codigo ya éxiste."], 409);
                }

                $repo->findByCrearTipoImputacion($request);
            }
            DB::commit();
            return response()->json(["message" => "Registro procesado correctamente"], 200);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'code' => $th->getCode(),
                'message' => $th->getMessage()
            ], 500);
        }
    }

    public function getAnularTipoImputacion(Request $request, ImputacionesPrestadoreRepository $repo)
    {
        $repo->findByDeleteTipoImputacion($request);
        return response()->json(["message" => "Registro eliminado correctamente"], 200);
    }
}
