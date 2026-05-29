<?php

namespace App\Http\Controllers\Contabilidad\Repository;

use App\Models\Contabilidad\AsientosContablesEntity;
use App\Models\Contabilidad\DetalleAsientosContablesEntity;
use App\Models\Contabilidad\ProveedorCuentaContableEntity;
use App\Models\Prestadores\PrestadorEntity;
use App\Models\proveedor\MatrizProveedoresEntity;
use App\Models\proveedor\ProveedorEntity;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class SaldoContableRepository
{

    private $user;
    private $fechaActual;

    public function __construct()
    {
        $this->user = Auth::user();
        $this->fechaActual = Carbon::now('America/Argentina/Buenos_Aires');
    }

    public function findByListarSaldos($params)
    {
        // Determinar si viene id_proveedor o id_prestador
        if (isset($params->id_proveedor) && !is_null($params->id_proveedor)) {
            // Paso 1: Buscar al proveedor (con el request)
            $proveedor = MatrizProveedoresEntity::where('cod_proveedor', $params->id_proveedor)->first();

            // Paso 3: Buscar detalles de asiento (con id_proveedor_cuenta_contable)
            $query = DetalleAsientosContablesEntity::with([
                'proveedorCuentaContable.proveedor',
                'planCuenta',
                'asientoContable'
            ])
                ->where('cod_proveedor', $params->id_proveedor);

        } elseif (isset($params->id_prestador) && !is_null($params->id_prestador)) {
            // Buscar por prestador
            $prestador = PrestadorEntity::where('cod_prestador', $params->id_prestador)->first();

            if (!$prestador) {
                return collect(); // Si no existe el prestador, retornar vacío
            }

            // Paso 3: Buscar detalles de asiento (con cod_prestador)
            $query = DetalleAsientosContablesEntity::with([
                'prestador',
                'planCuenta',
                'asientoContable'
            ])
                ->where('cod_prestador', $params->id_prestador);

        } else {
            return collect(); // Si no viene ninguno de los dos parámetros
        }

        // Filtros adicionales
        if (!is_null($params->id_periodo_contable)) {
            $query->whereHas('asientoContable', function ($q) use ($params) {
                $q->where('id_periodo_contable', $params->id_periodo_contable);
            });
        }

        if (!is_null($params->desde) && !is_null($params->hasta)) {
            $query->whereHas('asientoContable', function ($q) use ($params) {
                $q->whereBetween('fecha_asiento', [$params->desde, $params->hasta]);
            });
        }

        // Solo asientos activos
        $query->whereHas('asientoContable', function ($q) {
            $q->where('vigente', 'ACTIVO');
        });

        $query->orderByDesc('id_asiento_contable_detalle');

        $detallesRaw = $query->get();

        // Organizar los datos en el formato solicitado
        $resultado = [
            'proveedor' => [
                'cod_proveedor' => isset($proveedor) ? ($proveedor->cod_proveedor ?? null) : null,
                'cuit' => isset($proveedor) ? ($proveedor->cuit ?? null) : null,
                'razon_social' => isset($proveedor) ? ($proveedor->razon_social ?? null) : null,
                'nombre_fantasia' => isset($proveedor) ? ($proveedor->nombre_fantasia ?? null) : null
            ],
            'prestador' => isset($prestador) ? [
                'cod_prestador' => $prestador->cod_prestador ?? null,
                'nombre' => $prestador->nombre ?? null,
                'apellido' => $prestador->apellido ?? null,
                'razon_social' => $prestador->razon_social ?? null
            ] : null,
            'detalle' => $detallesRaw->map(function ($detalle) {
                return [
                    'id_asiento_contable_detalle' => $detalle->id_asiento_contable_detalle,
                    'id_asiento_contable' => $detalle->id_asiento_contable,
                    'id_proveedor_cuenta_contable' => $detalle->id_proveedor_cuenta_contable,
                    'monto_debe' => $detalle->monto_debe,
                    'monto_haber' => $detalle->monto_haber,
                    'observaciones' => $detalle->observaciones,
                    'id_detalle_plan' => $detalle->id_detalle_plan,
                    'recursor' => $detalle->recursor,

                    // Datos del asiento contable
                    'asiento_contable' => [
                        'id_asiento_contable' => $detalle->asientoContable->id_asiento_contable ?? null,
                        'id_tipo_asiento' => $detalle->asientoContable->id_tipo_asiento ?? null,
                        'fecha_asiento' => $detalle->asientoContable->fecha_asiento ?? null,
                        'asiento_modelo' => $detalle->asientoContable->asiento_modelo ?? null,
                        'asiento_leyenda' => $detalle->asientoContable->asiento_leyenda ?? null,
                        'asiento_observaciones' => $detalle->asientoContable->asiento_observaciones ?? null,
                        'id_periodo_contable' => $detalle->asientoContable->id_periodo_contable ?? null,
                        'numero' => $detalle->asientoContable->numero ?? null,
                        'numero_referencia' => $detalle->asientoContable->numero_referencia ?? null,
                        'vigente' => $detalle->asientoContable->vigente ?? null,
                        'fecha_registra' => $detalle->asientoContable->fecha_registra ?? null
                    ],

                    // Datos del plan de cuentas
                    'plan_cuenta' => [
                        'id_detalle_plan' => $detalle->planCuenta->id_detalle_plan ?? null,
                        'id_plan_cuenta' => $detalle->planCuenta->id_plan_cuenta ?? null,
                        'id_nivel_plan_cuenta' => $detalle->planCuenta->id_nivel_plan_cuenta ?? null,
                        'codigo_cuenta' => $detalle->planCuenta->codigo_cuenta ?? null,
                        'cuenta' => $detalle->planCuenta->cuenta ?? null,
                        'id_nivel_padre' => $detalle->planCuenta->id_nivel_padre ?? null,
                        'id_tipo_cuenta' => $detalle->planCuenta->id_tipo_cuenta ?? null,
                        'vigente' => $detalle->planCuenta->vigente ?? null,
                        'imputable' => $detalle->planCuenta->imputable ?? null,
                        'grupo' => $detalle->planCuenta->grupo ?? null,
                        'subgrupo' => $detalle->planCuenta->subgrupo ?? null
                    ]
                ];
            })->toArray(),

            // Resumen de saldos
            'resumen' => [
                'total_debe' => number_format($detallesRaw->sum('monto_debe'), 2, '.', ''),
                'total_haber' => number_format($detallesRaw->sum('monto_haber'), 2, '.', ''),
                'saldo_neto' => number_format($detallesRaw->sum('monto_debe') - $detallesRaw->sum('monto_haber'), 2, '.', ''),
                'cantidad_movimientos' => $detallesRaw->count(),
                'fecha_ultimo_movimiento' => $detallesRaw->first()->asientoContable->fecha_asiento ?? null
            ]
        ];

        return $resultado;
    }

}
