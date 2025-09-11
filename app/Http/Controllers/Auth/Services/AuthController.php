<?php

namespace App\Http\Controllers\Auth\Services;

use App\Http\Controllers\Auth\Repository\AuthrnticateRepository;
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

    public function login(Request $request, AuthrnticateRepository $repo)
    {
        $request->validate([
            'email' => 'required|string',
            'password' => 'required|string',
        ]);
        $credentials = $request->only('email', 'password');
        Log::info(Hash::make($credentials['password']));
        $token = $repo->findByIsLogin($credentials);
        if (!$token) {
            return response()->json([
                'status' => 'error',
                'message' => 'EL usuario y/ó contraseña ingresada son incorrectos.',
            ], 401);
        }

        $user = $repo->findByIsAuthenticate();
        if ($user->estado_cuenta == '0') {
            return response()->json([
                'status' => 'error',
                'message' => 'La cuenta se encuentra bloqueada.',
            ], 401);
        }

       /*  if ($user->cod_perfil == '11') {
            $repoApiexterno->getRegistrarPaciente($user->documento);
        } */

        return response()->json([
            'status' => 'success',
            'user' => $user,
            'authorisation' => [
                'token' => $token,
                'type' => 'bearer'
            ]
        ]);
    }

    public function getObtenerAcceso(AuthrnticateRepository $repo)
    {
        return response()->json($repo->findByAccesoModulos());
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
        $now = new \DateTime('now', new \DateTimeZone('America/Argentina/Buenos_Aires'));
        if ($request->password === $request->repeatPassword) {
            DB::update("UPDATE tb_usuarios set fecha_cambio_clave=?,  password = ? where email = ? ", [$now->format('Y-m-d H:i:s'), Hash::make($request->password), $request->email]);
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
}
