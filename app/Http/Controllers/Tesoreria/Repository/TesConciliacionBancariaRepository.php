<?php

namespace App\Http\Controllers\Tesoreria\Repository;

use App\Models\Tesoreria\TesConciliacionBancariaEntity;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TesConciliacionBancariaRepository
{
    private $user;
    private $fechaActual;

    public function __construct()
    {
        $this->user = Auth::user();
        $this->fechaActual = Carbon::now('America/Argentina/Buenos_Aires');
    }


    public function findByNueva($params)
    {
        return TesConciliacionBancariaEntity::create([
            'id_cuenta_bancaria' => $params->id_cuenta_bancaria,
            'monto_cuenta' => $params->monto_cuenta,
            'monto_contabilidad' => $params->monto_contabilidad,
            'id_usuario' => $this->user->cod_usuario,
            'fecha_registra' => $this->fechaActual,
            'observaciones' => $params->observaciones
        ]);
    }

    public function findByUpdate($params)
    {
        $conciliacion = TesConciliacionBancariaEntity::find($params->id_conciliacion);
        $conciliacion->id_cuenta_bancaria = $params->id_cuenta_bancaria;
        $conciliacion->monto_cuenta = $params->monto_cuenta;
        $conciliacion->monto_contabilidad = $params->monto_contabilidad;
        $conciliacion->id_usuario_modifica = $this->user->cod_usuario;
        $conciliacion->fecha_modifica = $this->fechaActual;
        $conciliacion->observaciones = $params->observaciones;
        $conciliacion->update();
        return $conciliacion;
    }

    public function findbyListBetween($desde, $hasta)
    {
        return TesConciliacionBancariaEntity::with(['cuenta', 'usuario'])
            ->whereBetween(DB::raw('DATE(fecha_registra)'), [$desde, $hasta])
            ->get();
    }

    public function findbyListBetweenAndCuenta($desde, $hasta, $cuenta)
    {
        return TesConciliacionBancariaEntity::with(['cuenta', 'usuario'])
            ->where('id_cuenta_bancaria', $cuenta)
            ->whereBetween(DB::raw('DATE(fecha_registra)'), [$desde, $hasta])
            ->get();
    }
}
