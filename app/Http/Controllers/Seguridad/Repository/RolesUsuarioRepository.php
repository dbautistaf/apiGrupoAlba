<?php

namespace App\Http\Controllers\Seguridad\Repository;

use App\Http\Controllers\Controller;
use App\Models\Seguridad\PermisoBotonesEntity;
use App\Models\Seguridad\RolesUsuarioEntity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class RolesUsuarioRepository
{
    //
    public function findByListRolesPermisos($request)
    {
        $btnAsignado = PermisoBotonesEntity::where('estado', '1')
            ->where('cod_menu', $request->cod_menu)->get();

        $array = [];
        foreach ($btnAsignado as $value) {
            $asignado = 0;
            $query = RolesUsuarioEntity::where('cod_usuario', $request->cod_usuario)
                ->where('cod_permisos', $value->cod_permisos)->first();

            if ($query) {
                $asignado = 1;
            }

            $array[] = array(
                'cod_roles' =>$query->cod_roles??null,
                'descripcion' => $value->descripcion,
                'cod_menu' => $value->cod_menu,
                "cod_usuario" => $request->cod_usuario??null,
                "cod_permisos" => $value->cod_permisos??null,
                "asignado" => $asignado,
                "estado" =>  $value->estado,
                "estado_rol" =>  $query->estado??null,
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
        if ($request->cod_roles == null) {
            RolesUsuarioEntity::create([
                'cod_usuario' => $request->cod_usuario,
                'cod_permisos' => $request->cod_permisos,
                'estado' => 1
            ]);
            return ["message" => "Nuevo Rol asignado con éxito"];
        } else {
            RolesUsuarioEntity::where('cod_roles', $request->cod_roles)->delete();
            return ["message" => "Se quitó el rol con éxito"];
        }
    }
    
    public function findByRolesPermisos()
    {
        $user = Auth::user();
        $permiso = DB::table('tb_roles_usuario as tr')
            ->join('tb_permiso_roles as tp', 'tp.cod_permisos', '=', 'tr.cod_permisos')
            ->where('tr.cod_usuario', 2)
            ->where('tp.estado', 1)
            ->select('tp.validar_btn')
            ->get();
        return $permiso;
    }
}
