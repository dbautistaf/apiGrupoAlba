<?php

namespace App\Http\Controllers\liquidaciones;

use App\Http\Controllers\liquidaciones\repository\LiqMatrizMedicamentosRepository;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class LiqMatrizMedicamentosController extends Controller
{

    public function postAlta(LiqMatrizMedicamentosRepository $repo, Request $request)
    {
        if (!is_null($request->id_medicamento)) {
            $repo->findByUpdate($request);
        } else {
            $repo->findBySave($request);
        }


        return response()->json(["message" => "Registro procesado correctamente"], 200);
    }

    public function getMatriz(LiqMatrizMedicamentosRepository $repo, Request $request)
    {
        $data = $repo->findbyListAlls(200);

        return response()->json($data, 200);
    }

    public function getListMatrizActivos(LiqMatrizMedicamentosRepository $repo, Request $request)
    {
        $data = [];

        if (!is_null($request->troquel) && is_null($request->medicamento)) {
            $data = $repo->findbyListTroquel($request->troquel, 10);
        } else if (is_null($request->troquel) && !is_null($request->medicamento)) {
            $data = $repo->findbyListMedicamento($request->medicamento, 10);
        } else {
            $data = $repo->findbyListAlls(50);
        }


        return response()->json($data, 200);
    }

    public function getEliminar(LiqMatrizMedicamentosRepository $repoLiq, Request $request)
    {
        $repoLiq->deleteId($request->id);
        return response()->json(["message" => "Registro eliminado correctamente"]);
    }
}
