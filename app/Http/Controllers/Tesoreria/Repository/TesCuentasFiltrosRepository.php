<?php

namespace App\Http\Controllers\Tesoreria\Repository;

use App\Models\Tesoreria\TesCuentasBancariasEntity;
use App\Models\Tesoreria\TesMovientosCuentaBancariaEntity;
use Illuminate\Support\Facades\DB;

class TesCuentasFiltrosRepository
{

    /**
     * Listado de cuentas con filtros COMBINABLES.
     *
     * Reemplaza al par findByListAlls/findByListBanco, que se elegian con un if y por eso nunca
     * podian aplicarse juntos: pedir banco descartaba la razon social. (2026-09-04)
     */
    public function findByListFiltrado($idRazon = null, $banco = null)
    {
        return TesCuentasBancariasEntity::with([
            'tipoCuenta', 'entidadBancaria', 'tipoMoneda', 'razonSocial',
            'cuentaContableBanco', 'cuentaContableEcheq',
        ])
            ->when($idRazon, fn($q) => $q->where('id_razon', $idRazon))
            ->when($banco, fn($q) => $q->where('id_entidad_bancaria', $banco))
            ->orderByDesc('id_cuenta_bancaria')
            ->get();
    }

    public function findByListAlls($idRazon = null)
    {
        return TesCuentasBancariasEntity::with([
            'tipoCuenta', 'entidadBancaria', 'tipoMoneda', 'razonSocial',
            // Para que el visor pueda avisar si la cuenta no esta relacionada contablemente.
            'cuentaContableBanco', 'cuentaContableEcheq',
        ])
            ->when($idRazon, fn ($q) => $q->where('id_razon', $idRazon))
            ->orderByDesc('id_cuenta_bancaria')
            ->get();
    }

    public function findByListBanco($banco)
    {
        return TesCuentasBancariasEntity::with([
            'tipoCuenta', 'entidadBancaria', 'tipoMoneda',
            'cuentaContableBanco', 'cuentaContableEcheq',
        ])
            ->where('id_entidad_bancaria', $banco)
            ->orderByDesc('id_cuenta_bancaria')
            ->get();
    }

    public function findById($id)
    {
        return TesCuentasBancariasEntity::with(['tipoCuenta', 'entidadBancaria', 'tipoMoneda'])
            ->find($id);
    }

    public function findByListMovimientos($desde, $hasta)
    {
        return TesMovientosCuentaBancariaEntity::with(['cuenta'])
            ->whereBetween(DB::raw('DATE(fecha_movimiento)'), [$desde, $hasta])
            ->orderByDesc('id_movimiento')
            ->get();
    }

    public function findByListMovimientosIdCuenta($desde, $hasta, $cuenta)
    {
        return TesMovientosCuentaBancariaEntity::with(['cuenta'])
            ->whereBetween(DB::raw('DATE(fecha_movimiento)'), [$desde, $hasta])
            ->where('id_cuenta_bancaria', $cuenta)
            ->orderByDesc('id_movimiento')
            ->get();
    }
}
