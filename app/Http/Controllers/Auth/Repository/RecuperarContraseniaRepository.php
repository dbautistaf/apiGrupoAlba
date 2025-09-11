<?php

namespace App\Http\Controllers\Auth\Repository;

use App\Models\Seguridad\RecuperarContraseniaEntity;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;

class RecuperarContraseniaRepository
{

    private $fechaActual;
    public function __construct()
    {
        $this->fechaActual = Carbon::now('America/Argentina/Buenos_Aires');
    }

    public function findByValidarCuenta($email)
    {
        return User::where('email', $email)
            ->first();
    }

    public function findByGnerarCodigoVerificador($cuenta)
    {
        $user = User::find($cuenta->cod_usuario);
        $user->codigo_verificacion = $this->findByGenerarCodigo(7);
        $user->update();
        return $user;
    }

    function findByGenerarCodigo($length)
    {
        $codigo = Str::upper(Str::random($length));
        return $codigo;
    }

    public function findByRegistrarEvento($user, $ip, $navegador, $hosname)
    {
        return RecuperarContraseniaEntity::create([
            'fecha_solicita' => $this->fechaActual,
            'codigo_verificador' => $user->codigo_verificacion,
            'email' => $user->email,
            'jwt' => $this->generarJWTRestablecerContraseña($user, $user->codigo_verificacion),
            'ip_equipo' => $ip,
            'navegador' => $navegador,
            'hosname' => $hosname
        ]);
    }

    function generarJWTRestablecerContraseña($usuarioId, $digito)
    {
        $expiracion = 10; // Tiempo en minutos
        $fechaExpiracion = Carbon::now()->addMinutes($expiracion)->timestamp;
        $token = JWTAuth::customClaims([
            'exp' => $fechaExpiracion, // Establecer el claim 'exp' manualmente
            'dv' => $digito
        ])->fromUser($usuarioId);
        return $token;
    }

    public function findByUpdateContrasenia($cuenta, $password)
    {
        $user = User::find($cuenta->cod_usuario);
        $user->estado_cuenta = '1';
        $user->fecha_cambio_clave = $this->fechaActual;
        $user->password = Hash::make($password);
        $user->update();
        return $user;
    }

    public function findByValidarDigitoVerificador($digito)
    {
        return  RecuperarContraseniaEntity::where('codigo_verificador', $digito)->exists();
    }

    public function findByFinalizarEvento($digito)
    {
        $evento = RecuperarContraseniaEntity::where('codigo_verificador', $digito)->first();
        $evento->jwt = 'JWT - USADO - ELIMINADO';
        $evento->fecha_cambio_clave = $this->fechaActual;
        $evento->update();
    }

    function findByIsTokenLogin($usuarioId)
    {
        $expiracion = 60; // Tiempo en minutos
        $fechaExpiracion = Carbon::now()->addMinutes($expiracion)->timestamp;
        $token = JWTAuth::fromUser($usuarioId, ['exp' => $fechaExpiracion]);
        return $token;
    }
}
