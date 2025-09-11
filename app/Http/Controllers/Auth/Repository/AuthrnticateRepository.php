<?php
namespace App\Http\Controllers\Auth\Repository;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;

class AuthrnticateRepository
{
    // composer require firebase/php-jwt

    public function findByIsLogin($credentials)
    {
        return Auth::attempt($credentials);
    }

    public function findByIsAuthenticate()
    {
        return Auth::user();
    }

    public function findByAccesoModulos()
    {
        try {
            $user = $this->findByIsAuthenticate();
            $perfil = $user->cod_perfil;
            $menu = DB::select(
                "SELECT * FROM vw_menu_acceso_usuario WHERE menu_estado = 1 AND  menu_principal = ? AND cod_perfil = ? AND estado_acceso = ? ORDER BY  menu_orden ASC",
                [1, $perfil, 1]
            );

            $jsonarray = array();
            foreach ($menu as $key) {
                $submenu = DB::select(
                    "SELECT * FROM vw_menu_acceso_usuario WHERE menu_estado = 1 AND menu_principal IN ('0','2') AND cod_perfil = ? AND menu_grupo = ? AND estado_acceso = ? ORDER BY  menu_orden ASC",
                    [$perfil, $key->menu_grupo, 1]
                );

                $jsonSubMenu = array();

                foreach ($submenu as $value) {
                    $subItems = array();
                    if ($value->menu_principal == '2') {
                        $treeMenu = DB::select(
                            "SELECT * FROM vw_menu_acceso_usuario WHERE menu_estado = 1 AND menu_principal = '-' AND tipo_ruta = ? AND cod_perfil = ? AND menu_grupo = ? AND estado_acceso = ? ORDER BY  menu_orden ASC",
                            [$value->tipo_ruta, $perfil, $key->menu_grupo, 1]
                        );

                        foreach ($treeMenu as $tree) {
                            $subItems[] = array(
                                "routerLink" => $tree->menu_link,
                                "label" => $tree->menu_descripcion
                            );
                        }
                    }

                    $jsonSubMenu[] = array(
                        "routerLink" => $value->menu_link,
                        "label" => $value->menu_descripcion,
                        "items" => $subItems
                    );
                }

                $jsonarray[] = array(
                    "routerLink" => $key->menu_link,
                    "icon" => $key->menu_icono,
                    "label" => $key->menu_descripcion,
                    "items" => $jsonSubMenu
                );
            }
            return $jsonarray;
        } catch (\Throwable $th) {
            return response()->json([
                'code' => $th->getCode(),
                'message' => $th->getMessage()
            ], 400);
        }
    }

    public function findByEncriptAccesoMenu($data)
    {
        $key = env('JWT_SECRET');

        $payload = [
            'data' => $data,
            'iat' => time(),
            'exp' => time() + 7200
        ];

        $token = JWTAuth::encode($payload, $key, 'HS256');
        return $token;
    }
}
