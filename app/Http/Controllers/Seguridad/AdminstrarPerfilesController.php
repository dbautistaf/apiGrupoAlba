<?php

namespace App\Http\Controllers\Seguridad;

use App\Http\Controllers\Seguridad\Repository\PerfilesRepository;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class AdminstrarPerfilesController extends Controller
{

    public function getListarPerfiles(PerfilesRepository $repo)
    {
        return response()->json($repo->findByListAlls());
    }

    public function getProcesarPerfil(PerfilesRepository $repo, Request $request)
    {
        if (!is_null($request->cod_perfil)) {
            $repo->findByActualizarId($request);
            return response()->json(["message" => "Perfil actualizado correctamente"]);
        } else {
            $repo->findByCrear($request);
            return response()->json(["message" => "Perfil creado correctamente"]);
        }
    }

    public function getEliminarPerfil(PerfilesRepository $repo, Request $request)
    {
        if ($repo->findByExistePerfilAsignadoId($request->id)) {
            return response()->json(["message" => "El perfil se encuentra asignado a un usuario. Verificar antes de eliminar"], 409);
        } else {
            $repo->findByEliminarId($request->id);
            return response()->json(["message" => "Perfil eliminado correctamente"]);
        }
    }
}
