<?php

namespace App\Http\Controllers\Seguridad\Repository;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;

class UsuarioRepository
{

    private $fechaActual;
    public function __construct()
    {
        $this->fechaActual = Carbon::now('America/Argentina/Buenos_Aires');
    }

    public function findByListAndLimitPage($limit)
    {
        return User::with(['perfil'])
            ->orderByDesc('estado_cuenta')
            ->limit($limit)
            ->get();
    }

    public function findByListAndPerfilAndLimitPage($perfil, $limit)
    {
        return User::with(['perfil'])
            ->where('cod_perfil', $perfil)
            ->orderBy('nombre_apellidos')
            ->limit($limit)
            ->get();
    }

    public function findByListAndPerfilAndEstadoAndLimitPage($perfil, $estado, $limit)
    {
        return User::with(['perfil'])
            ->where('cod_perfil', $perfil)
            ->where('estado_cuenta', $estado)
            ->orderBy('nombre_apellidos')
            ->limit($limit)
            ->get();
    }

    public function findByListAndNombresOrEmailAndLimitPage($search, $limit)
    {
        return User::with(['perfil'])
            ->where(function ($query) use ($search) {
                $query->orWhere('nombre_apellidos', 'LIKE', $search . '%')
                    ->orWhere('email', 'LIKE', $search . '%');
            })
            ->orderBy('nombre_apellidos')
            ->limit($limit)
            ->get();
    }

    public function findByListAndPerfilAndEstadoAndNombresOrEmailAndLimitPage($perfil, $estado, $search, $limit)
    {
        return User::with(['perfil'])
            ->where('cod_perfil', $perfil)
            ->where('estado_cuenta', $estado)
            ->where(function ($query) use ($search) {
                $query->orWhere('nombre_apellidos', 'LIKE', $search . '%')
                    ->orWhere('email', 'LIKE', $search . '%');
            })
            ->orderBy('nombre_apellidos')
            ->limit($limit)
            ->get();
    }

    public function findByListEstadoAndNombresOrEmailAndLimitPage($estado, $search, $limit)
    {
        return User::with(['perfil'])
            ->where('estado_cuenta', $estado)
            ->where(function ($query) use ($search) {
                $query->orWhere('nombre_apellidos', 'LIKE', $search . '%')
                    ->orWhere('email', 'LIKE', $search . '%');
            })
            ->orderBy('nombre_apellidos')
            ->limit($limit)
            ->get();
    }

    public function findByListAndPerfilAndNombresOrEmailAndLimitPage($perfil, $search, $limit)
    {
        return User::with(['perfil'])
            ->where('cod_perfil', $perfil)
            ->where(function ($query) use ($search) {
                $query->orWhere('nombre_apellidos', 'LIKE', $search . '%')
                    ->orWhere('email', 'LIKE', $search . '%');
            })
            ->orderBy('nombre_apellidos')
            ->limit($limit)
            ->get();
    }

    public function findByListAndEstadoAndLimitPage($estado, $limit)
    {
        return User::with(['perfil'])
            ->where('estado_cuenta', $estado)
            ->limit($limit)
            ->get();
    }

    public function findByCrear($params)
    {
        return User::create([
            'nombre_apellidos' => $params->nombre_apellidos,
            'documento' => $params->documento,
            'telefono' => $params->telefono,
            'direccion' => $params->direccion,
            'fecha_alta' => $params->fecha_alta,
            'fecha_baja' => $params->fecha_baja,
            'estado_cuenta' => $params->estado_cuenta,
            'fecha_cambio_clave' => ($params->fecha_cambio_clave ?? $this->fechaActual),
            'email' => $params->email,
            'codigo_verificacion' => $params->codigo_verificacion,
            'password' => bcrypt($params->password),
            'cod_perfil' => $params->cod_perfil
        ]);
    }

    public function findByActualizar($params)
    {
        $user = User::find($params->cod_usuario);
        $user->nombre_apellidos = $params->nombre_apellidos;
        $user->documento = $params->documento;
        $user->telefono = $params->telefono;
        $user->direccion = $params->direccion;
        $user->fecha_baja = $params->fecha_baja;
        $user->estado_cuenta = $params->estado_cuenta;
        $user->email = $params->email;
        $user->codigo_verificacion = $params->codigo_verificacion;
        $user->cod_perfil = $params->cod_perfil;
        $user->fecha_registra = ($params->fecha_registra ?? $this->fechaActual);
        $user->update();
        return $user;
    }

    public function findByDeshabilitarCuenta($params)
    {
        $user = User::where('email', $params->email)->first();
        $user->estado_cuenta = '0';
        $user->update();
        return $user;
    }

    public function findByHabilitarCuenta($params)
    {
        $user = User::where('email', $params->email)->first();
        $user->fecha_baja = null;
        $user->estado_cuenta = '1';
        $user->update();
        return $user;
    }

    public function findByCambiarClaveCuenta($params)
    {
        $user = User::where('email', $params->email)->first();
        $user->password = Hash::make($params->password);
        $user->fecha_cambio_clave = $this->fechaActual;
        $user->update();
        return $user;
    }

    public function findByValidarCuenta($email)
    {
        return User::where('email', $email)->exists();
    }
}
