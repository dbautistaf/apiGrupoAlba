<?php

namespace App\Http\Controllers\Auth\Services;

use App\Http\Controllers\Apis\Services\ApiPrescriptorService;
use App\Http\Controllers\Auth\Repository\AuthrnticateRepository;
use App\Models\Seguridad\AccesoUsuarioEntity;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;

class AuthController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login', 'register', 'loginApis']]);
    }

    public function login(Request $request, AuthrnticateRepository $repo)
    {
        $request->validate([
            'email' => 'required|string',
            'password' => 'required|string',
        ]);
        $credentials = $request->only('email', 'password');

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

        /* if ($user->cod_perfil == '25') {
            return response()->json(['success' => false, 'message' => 'No cuenta con los permisos suficientes para ingresar a esta plataforma'], 401);
        } */

        $ip = request()->ip();
        $agent = request()->header('User-Agent');
        $agentInfo = \UAParser\Parser::create()->parse($agent);

        AccesoUsuarioEntity::create([
            'cod_usuario' => $user->cod_usuario,
            'navegador' => $agentInfo->ua->toString(),
            'plataforma' => $agentInfo->os->toString(),
            'device' => $agentInfo->device->family ?? 'Desconocido',
            'ip' => $ip,
            'fecha_acceso' => Carbon::now()
        ]);

        return response()->json([
            'status' => 'success',
            'user' => $user,
            'authorisation' => [
                'token' => $token,
                'type' => 'bearer'
            ]
        ]);
    }

    public function loginApis(Request $request, AuthrnticateRepository $repo)
    {
        $request->validate([
            'email' => 'required|string',
            'password' => 'required|string',
        ]);
        $credentials = $request->only('email', 'password');

        $token = $repo->findByIsLogin($credentials);
        if (!$token) {
            return response()->json([
                'success' => false,
                'message' => 'EL usuario y/ó contraseña ingresada son incorrectos.',
            ], 401);
        }

        $user = $repo->findByIsAuthenticate();
        if ($user->estado_cuenta == '0') {
            return response()->json([
                'success' => false,
                'message' => 'La cuenta se encuentra bloqueada.',
            ], 401);
        }

        return response()->json([
            'success' => true,
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

    public function controlAcceso(Request $request)
    {
        $data = AccesoUsuarioEntity::with(['usuario'])
            ->whereHas('usuario', function ($q) use ($request) {
                $q->where('cod_perfil', 23);
            })
            ->whereBetween('fecha_acceso', [$request->desde, $request->hasta])
            ->orderByDesc('id_acceso')
            ->get();
        return response()->json($data);
    }
}
