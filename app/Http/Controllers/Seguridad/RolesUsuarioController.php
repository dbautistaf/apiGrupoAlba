<?php

namespace App\Http\Controllers\Seguridad;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Seguridad\Repository\RolesUsuarioRepository;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as RoutingController;

class RolesUsuarioController extends RoutingController
{
    //
     public function getListarRoles(RolesUsuarioRepository $repo, Request $request)
    {
        return response()->json($repo->findByListRolesPermisos($request));
    }

    public function postSaveNewPermisos(RolesUsuarioRepository $repo, Request $request)
    {
        response()->json($repo->findBySave($request));
        return response()->json(["message" => "Nuevo Permisos agregado con éxito"]);
    }

    public function postSaveRolesPermisos(RolesUsuarioRepository $repo, Request $request)
    {
        return response()->json($repo->findBySaveRolesUser($request));
    }

    public function getRolesUsuarios(RolesUsuarioRepository $repo)
    {
        return response()->json($repo->findByRolesPermisos());
    }
}
