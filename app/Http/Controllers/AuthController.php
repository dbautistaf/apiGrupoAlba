<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login', 'register']]);
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|string',
            'password' => 'required|string',
        ]);
        $credentials = $request->only('email', 'password');

        $token = Auth::attempt($credentials);
        if (!$token) {
            return response()->json([
                'status' => 'error',
                'message' => 'EL usuario y/ó contraseña ingresada son incorrectos.',
            ], 401);
        }

        $user = Auth::user();
        if ($user->estado_cuenta == '0') {
            return response()->json([
                'status' => 'error',
                'message' => 'La cuenta se encuentra bloqueada.',
            ], 401);
        }
        return response()->json([
            'status' => 'success',
            'user' => Auth::user(),
            'authorisation' => [
                'token' => $token,
                'type' => 'bearer',
            ]
        ]);
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $token = Auth::login($user);
        return response()->json([
            'status' => 'success',
            'message' => 'User created successfully',
            'user' => $user,
            'authorisation' => [
                'token' => $token,
                'type' => 'bearer',
            ]
        ]);
    }

    public function logout()
    {
        Auth::logout();
        return response()->json([
            'status' => 'success',
            'message' => 'Muy bien se proceso a cerrar su sesión con éxito.',
        ]);
    }

    public function postCambiarContraseña(Request $request)
    {
        if ($request->password === $request->repeatPassword) {
            DB::update("UPDATE tb_usuarios set  password = ? where email = ? ", [Hash::make($request->password), $request->email]);
        } else {
            return response()->json([
                'message' => 'Las credenciales no coinciden',
            ], 409);
        }

        return response()->json([
            'message' => 'Se proceso el cambio de sus credenciales.',
        ], 200);
    }

    public function refresh()
    {
        return response()->json([
            'status' => 'success',
            'user' => Auth::user(),
            'authorisation' => [
                'token' => Auth::refresh(),
                'type' => 'bearer',
            ]
        ]);
    }

    public function srvMenuAcceso($id_tipo_usuario)
    {
        try {
            $menu = DB::select(
                "SELECT * FROM vw_menu_acceso_usuario WHERE menu_estado = 1 AND  menu_principal = ? AND cod_perfil = ? AND estado_acceso = ? ORDER BY  menu_descripcion	ASC",
                [1, $id_tipo_usuario, 1]
            );

            $jsonarray =  array();
            foreach ($menu as $key) {
                $submenu = DB::select(
                    "SELECT * FROM vw_menu_acceso_usuario WHERE menu_estado = 1 AND menu_principal IN ('0','2') AND cod_perfil = ? AND menu_grupo = ? AND estado_acceso = ? ORDER BY  menu_descripcion ASC",
                    [$id_tipo_usuario, $key->menu_grupo, 1]
                );

                $jsonSubMenu = array();

                foreach ($submenu as $value) {
                    $subItems = array();
                    if ($value->menu_principal == '2') {
                        $treeMenu = DB::select(
                            "SELECT * FROM vw_menu_acceso_usuario WHERE menu_estado = 1 AND menu_principal = '-' AND tipo_ruta = ? AND cod_perfil = ? AND menu_grupo = ? AND estado_acceso = ? ORDER BY  menu_descripcion ASC",
                            [$value->tipo_ruta, $id_tipo_usuario, $key->menu_grupo, 1]
                        );

                        foreach ($treeMenu as $tree) {
                            $subItems[] = array(
                                "routerLink" => $tree->menu_link,
                                "label" =>  $tree->menu_descripcion
                            );
                        }
                    }

                    $jsonSubMenu[] = array(
                        "routerLink" => $value->menu_link,
                        "label" =>  $value->menu_descripcion,
                        "items" =>  $subItems
                    );
                }

                $jsonarray[] = array(
                    "routerLink" => $key->menu_link,
                    "icon" => $key->menu_icono,
                    "label" =>  $key->menu_descripcion,
                    "items" =>  $jsonSubMenu
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
}
