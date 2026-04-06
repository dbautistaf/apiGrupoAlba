<?php

namespace App\Http\Controllers\Seguridad\Repository;

use App\Http\Controllers\Controller;
use App\Models\Seguridad\PermisoBotonesEntity;
use App\Models\Seguridad\RolesUsuarioEntity;
use Illuminate\Http\Request;

class RolesUsuarioRepository
{
    //
    public function findByListRolesPermisos($request)
    {
        $btnAsignado = PermisoBotonesEntity::where('estado', '1')
            ->where('cod_menu', $request->cod_menu)->get();

        $array = [];
        foreach ($btnAsignado as $value) {
            $estado = 0;
            $query = RolesUsuarioEntity::where('cod_usuario', $request->cod_usuario)
                ->where('cod_menu', $value->cod_permisos)
                ->first();

            if ($query) {
                $estado = 1;
            }

            $array[] = array(
                'descripcion' => $value->descripcion,
                'cod_menu' => $value->cod_menu,
                "cod_usuario" => $query->cod_usuario,
                "cod_permisos" => $query->cod_permisos,
                "asignado" => $estado
            );
        }

        $array = collect($array);
        $array = $array->sortByDesc('descripcion')->values();
        return response()->json($array);
    }

    public function findBySave($request)
    {
        return PermisoBotonesEntity::create([
            'descripcion' => $request->descripcion,
            'cod_menu' => $request->cod_menu,
            'estado' => 1
        ]);
    }

    public function findBySaveRolesUser($request)
    {
        return RolesUsuarioEntity::create([
            'cod_usuario' => $request->descripcion,
            'cod_permisos' => $request->cod_menu
        ]);
    }
}
