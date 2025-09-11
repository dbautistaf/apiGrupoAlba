<?php

namespace  App\Http\Controllers\Protesis\Services;

use App\Http\Controllers\Protesis\Repository\MatrizDiagnosticosRepository;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class MatrizDiagnosticosController extends Controller
{

    public function getProcesar(MatrizDiagnosticosRepository $repo, Request $request)
    {
        if ($repo->findByExisteId($request->identificador)) {
            $repo->saveId($request);
            return response()->json(["message" => "Registro actualizado correctamente."]);
        } else {
            $repo->save($request);
            return response()->json(["message" => "Registro procesado correctamente."]);
        }
    }

    public function getEliminar(MatrizDiagnosticosRepository $repo, Request $request)
    {
        if ($repo->findByExisteId($request->identificador)) {
            $repo->findByIdDelete($request->identificador);
            return response()->json(["message" => "Registro eliminado correctamente."]);
        } else {
            return response()->json(["message" => "Registro no encontrado."], 404);
        }
    }

    public function getListar(MatrizDiagnosticosRepository $repo, Request $request)
    {
        $data = [];
        if (!is_null($request->identificador) && is_null($request->descripcion)) {
            $data = $repo->findByListIdentificadorLikeAndPaginate($request->identificador, 10);
        } else if (is_null($request->identificador) && !is_null($request->descripcion)) {
            $data = $repo->findByListDescripcionLikeAndPaginate($request->descripcion, 10);
        } else {
            $data = $repo->findByListPaginate(200);
        }

        return response()->json($data);
    }
}
