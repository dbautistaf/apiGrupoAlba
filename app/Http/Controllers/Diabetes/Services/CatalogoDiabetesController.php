<?php

namespace App\Http\Controllers\Diabetes\Services;

use App\Http\Controllers\Diabetes\Repository\CatalogoDiabetesRepository;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;

class CatalogoDiabetesController extends Controller
{
    public function getListaTipoDiabetes(CatalogoDiabetesRepository $repository)
    {
        return response()->json($repository->findByListTipoDiabetes());
    }

    public function getMedicamentos(Request $request, CatalogoDiabetesRepository $repository)
    {
        return response()->json($repository->findByListMedicamentos());
    }

    public function getMedicamentosMatriz(Request $request, CatalogoDiabetesRepository $repository)
    {
        return response()->json($repository->findByListMedicamentosMatriz($request->search));
    }

    public function getProcesarTipo(Request $request, CatalogoDiabetesRepository $repo)
    {
        DB::beginTransaction();
        try {
            if (!is_null($request->id_medicamento)) {
                $repo->findByUpdateMedicamento($request);
            } else {
                /*  if ($repo->findByExistsTipoImputacion($request->codigo)) {
                    DB::rollBack();
                    return response()->json(["message" => "El codigo de imputación $request->codigo ya éxiste."], 409);
                } */

                $repo->findByCrearMedicamento($request);
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

    public function getAnularTipoImputacion(Request $request, CatalogoDiabetesRepository $repo)
    {
        $repo->findByDeleteMedicamento($request);
        return response()->json(["message" => "Registro eliminado correctamente"], 200);
    }
}
