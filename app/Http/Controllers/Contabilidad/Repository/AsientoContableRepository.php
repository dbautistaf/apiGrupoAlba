<?php

namespace App\Http\Controllers\Contabilidad\Repository;

use App\Models\Contabilidad\AsientosContablesEntity;
use App\Models\Contabilidad\DetalleAsientosContablesEntity;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class AsientoContableRepository
{

    private $user;
    private $fechaActual;
    public function __construct()
    {
        $this->user = Auth::user();
        $this->fechaActual = Carbon::now();
    }

    public function findByCrearAsiento($id_tipo_asiento, $asiento_modelo, $asiento_leyenda, $numero, $id_periodo_contable, $numero_referencia, $vigente)
    {
        return AsientosContablesEntity::create([
            'id_tipo_asiento' => $id_tipo_asiento,
            'fecha_asiento' => $this->fechaActual,
            'asiento_modelo' => $asiento_modelo,
            'asiento_leyenda' => $asiento_leyenda,
            'numero' => $numero,
            'numero_referencia' => $numero_referencia,
            'id_periodo_contable' => $id_periodo_contable,
            'cod_usuario_crea' => $this->user->cod_usuario,
            'fecha_registra' => $this->fechaActual,
            'vigente' => $vigente
        ]);
    }

    public function findByCrearDetalleAsiento($params)
    {
        return DetalleAsientosContablesEntity::create([
            'id_asiento_contable' => $params['id_asiento_contable'],
            'id_proveedor_cuenta_contable' => $params['id_proveedor_cuenta_contable'],
            'id_forma_pago_cuenta_contable' => $params['id_forma_pago_cuenta_contable'],
            'monto_debe' => $params['monto_debe'],
            'monto_haber' => $params['monto_haber'],
            'observaciones' => $params['observaciones'],
            'id_detalle_plan' => $params['id_detalle_plan'],
            'recursor' => (int) $params['monto_debe'] > 0 ? '1' : '0'
        ]);
    }

    public function findByUpdateAsiento(
        $id_tipo_asiento,
        $fecha_asiento,
        $asiento_modelo,
        $asiento_leyenda,
        $numero,
        $id_periodo_contable,
        $numero_referencia,
        $asiento_observaciones,
        $idAsiento
    ) {
        $asiento = AsientosContablesEntity::find($idAsiento);
        $asiento->id_tipo_asiento = $id_tipo_asiento;
        $asiento->fecha_asiento = $fecha_asiento;
        $asiento->asiento_modelo = $asiento_modelo;
        $asiento->asiento_leyenda = $asiento_leyenda;
        $asiento->numero = $numero;
        $asiento->numero_referencia = $numero_referencia;
        $asiento->asiento_observaciones = $asiento_observaciones;
        $asiento->id_periodo_contable = $id_periodo_contable;
        $asiento->cod_usuario_modifica = $this->user->cod_usuario;
        $asiento->fecha_modifica = $this->fechaActual;
        return $asiento->update();
    }

    public function findByUpdateDetalleItemAsiento($params, $id)
    {
        $item = DetalleAsientosContablesEntity::find($id);
        $item->id_asiento_contable = $params['id_asiento_contable'];
        $item->id_proveedor_cuenta_contable = $params['id_proveedor_cuenta_contable'];
        $item->id_forma_pago_cuenta_contable = $params['id_forma_pago_cuenta_contable'];
        $item->monto_debe = $params['monto_debe'];
        $item->monto_haber = $params['monto_haber'];
        $item->observaciones = $params['observaciones'];
        $item->id_detalle_plan = $params['id_detalle_plan'];
        return $item->update();
    }

    public function findByListar($params)
    {
        $query = AsientosContablesEntity::with(['tipo']);

        if (!is_null($params->id_periodo_contable)) {
            $query->where('id_periodo_contable', [$params->id_periodo_contable]);
        }

        if (!is_null($params->numero)) {
            $query->where('numero', 'LIKE', "%$params->numero");
        }

        if (!is_null($params->desde) && !is_null($params->hasta)) {
            $query->whereBetween('fecha_asiento', [$params->desde, $params->hasta]);
        }

        if (!is_null($params->leyenda)) {
            $query->where('asiento_leyenda', 'LIKE', "%$params->leyenda%");
        }

        $query->orderByDesc('numero');

        return $query->get();
    }

    public function findById($id)
    {
        return AsientosContablesEntity::with(['detalle', 'detalle.planCuenta'])
            ->find($id);
    }

    public function findByDeleteDetalleId($id)
    {
        return DetalleAsientosContablesEntity::find($id)->delete();
    }

    public function findByAnularAsientoContableId($id, $vigente)
    {
        $asiento = AsientosContablesEntity::find($id);
        $asiento->vigente = $vigente;
        return $asiento->update();
    }

    public function findByContraAsientoContableId($numero, $numero_referencia, $vigente)
    {
        $asiento = AsientosContablesEntity::where('numero', [$numero])->first();
        $asiento->numero_referencia = $numero_referencia;
        $asiento->vigente = $vigente;
        return $asiento->update();
    }
}
