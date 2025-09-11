<?php

namespace   App\Http\Controllers\Protesis\Services;

use App\Http\Controllers\Protesis\Repository\CategoriaProductosRepository;
use App\Http\Controllers\Protesis\Repository\MatrizProductosRepository;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class   MatrizProductoController extends Controller
{

    public function getProcesar(MatrizProductosRepository $repo, Request $request)
    {
        if (!is_null($request->id_producto)) {
            $repo->saveId($request);
            return response()->json(["message" => "Producto actualizado correctamente"]);
        } else {
            $repo->save($request);
            return response()->json(["message" => "Producto creado correctamente"]);
        }
    }

    public function getEliminar(MatrizProductosRepository $repo, Request $request)
    {
        if ($repo->fidnByExistId($request->id_producto)) {
            $repo->findByEliminarId($request->id_producto);
            return response()->json(["message" => "Producto eliminado correctamente"]);
        } else {
            return response()->json(["message" => "Producto no encontrado"], 404);
        }
    }

    public function getListarProductos(MatrizProductosRepository $repo, Request $request)
    {
        $data = [];
        if (!is_null($request->categoria) && is_null($request->search)) {
            $data = $repo->findByListIdCategria($request->categoria, $request->limit);
        } else    if (is_null($request->categoria) && !is_null($request->search)) {
            $data = $repo->findByListIdCategria($request->categoria, $request->limit);
        } else if (is_null($request->categoria) && !is_null($request->search)) {
            $data = $repo->findByListProductoLike($request->search, $request->limit);
        } else {
            $data = $repo->findByListTodos($request->limit);
        }
        return response()->json($data);
    }

    public function getListarCategoriasProductos(CategoriaProductosRepository $repo)
    {
        $data = [];
        $data = $repo->findByListAlls();

        return response()->json($data);
    }
}
