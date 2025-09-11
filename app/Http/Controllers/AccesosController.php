<?php

namespace App\Http\Controllers;

use App\Models\MenuAccesoUsuarioModelo;
use App\Models\MenuModelo;
use App\Models\PerfilModelo;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AccesosController extends Controller
{
    //

    public function listPerfiles()
    {
        $listaperfil = PerfilModelo::where('estado', 1)->get();
        return response()->json($listaperfil, 200);
    }

    public function getListAccesoMenu()
    {
        $user = Auth::user();
        $submenu =  array();
        $menu =  array();
        $listamenu = MenuModelo::where('menu_principal', 0)
            ->where('menu_estado', '=', '1')
            ->get();
        foreach ($listamenu as $menus) {
            $query = MenuAccesoUsuarioModelo::where('cod_perfil', $user->cod_perfil)
                ->where('cod_menu', '=', $menus->cod_menu)->first();
            if ($query) {
                $submenu[] = array(
                    "cod_menu" => $menus->cod_menu,
                    "menu_descripcion" => $menus->menu_descripcion,
                    "menu_estado" =>  $menus->menu_estado,
                    "menu_grupo" =>  $menus->menu_grupo,
                    "menu_icono" =>   $menus->menu_icono,
                    "menu_link" =>  $menus->menu_link,
                    "menu_orden" =>  $menus->menu_orden,
                    "menu_principal" =>  $menus->menu_principal,
                    "tipo_ruta" =>  $menus->tipo_ruta
                );
            }
        }

        $menuPrincipal = MenuModelo::where('tb_menus.menu_principal', 1)->get();
        if ($menuPrincipal) {
            foreach ($menuPrincipal as $principal) {
                $menu[] = array(
                    "cod_menu" => $principal->cod_menu,
                    "menu_descripcion" => $principal->menu_descripcion,
                    "menu_estado" =>  $principal->menu_estado,
                    "menu_grupo" =>  $principal->menu_grupo,
                    "menu_icono" =>   $principal->menu_icono,
                    "menu_link" =>  $principal->menu_link,
                    "menu_orden" =>  $principal->menu_orden,
                    "menu_principal" =>  $principal->menu_principal,
                    "tipo_ruta" =>  $principal->tipo_ruta,
                    "submenu" => ""
                );
            }
        }

        return response()->json([$menu, $submenu], 200);
    }

    public function listMenu($idPerfil)
    {
        $array =  array();
        $listamenu = MenuModelo::where('menu_estado', 1)->get();
        foreach ($listamenu as $menu) {
            $estado = 0;
            $query = MenuAccesoUsuarioModelo::where('cod_perfil', $idPerfil)
                ->where('cod_menu', '=', $menu->cod_menu)->first();
            if ($query) {
                $estado = 1;
            }
            $array[] = array(
                "cod_menu" => $menu->cod_menu,
                "menu_descripcion" => $menu->menu_descripcion,
                "menu_estado" =>  $menu->menu_estado,
                "menu_grupo" =>  $menu->menu_grupo,
                "menu_icono" =>   $menu->menu_icono,
                "menu_link" =>  $menu->menu_link,
                "menu_orden" =>  $menu->menu_orden,
                "menu_principal" =>  $menu->menu_principal,
                "tipo_ruta" =>  $menu->tipo_ruta,
                "asignado" => $estado
            );
        }

        return response()->json($array, 200);
    }

    public function saveAccesos(Request $request)
    {
        if ($request->checked != true) {
            $query = MenuAccesoUsuarioModelo::where('cod_perfil', $request->perfil)
                ->where('cod_menu', '=', $request->cod_menu)->first();
            if ($query != '') {
                MenuAccesoUsuarioModelo::where('cod_perfil', $request->perfil)->where('cod_menu', '=', $request->cod_menu)->delete();
                $msg = 'Menu denegado correctamente';
            }
        } else {
            MenuAccesoUsuarioModelo::create([
                'cod_menu' => $request->cod_menu,
                'cod_perfil' => $request->perfil,
                'estado_acceso' => 1
            ]);
            $msg = 'Menu asignado correctamente';
        }
        return response()->json(['message' => $msg], 200);
    }

    public function estadoMenu(Request $request)
    {
        MenuModelo::where('cod_menu', $request->id)->update(['menu_estado' => $request->activo]);
        return response()->json(['message' => 'Estado cambiado correctamente'], 200);
    }

    public function validarActualizacionDatos()
    {
        $user = Auth::user();
        if ($user->actualizo_datos != 1 && $user->cod_perfil == 25) {
            return response()->json(["isUpdateData" => 'NO',"User"=>$user->cod_perfil], 200);
        }
        return response()->json(["isUpdateData" => 'SI'], 200);
    }
}
