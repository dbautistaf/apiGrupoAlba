<?php

namespace App\Observers;

use App\Models\auditoria\AuditarFacturacionEntity;
use App\Models\facturacion\FacturacionDetalleDescuentoEntity;
use Carbon\Carbon;

class AuditFacturacionDescuentosObserver
{

    public function created(FacturacionDetalleDescuentoEntity $model)
    {
        $fechaActual = Carbon::now('America/Argentina/Buenos_Aires');
        AuditarFacturacionEntity::create([
            'nombre_tabla' => "tb_facturacion_detalle_descuento",
            'id_tabla' => $model->id_detalle_descuento,
            'data_anterior' => null,
            'data_nueva' => json_encode($model->getAttributes()),
            'cod_usuario' => auth()->id(),
            'tipo_accion' => 'CREATE',
            'fecha_accion' => $fechaActual
        ]);
    }


    public function updated(FacturacionDetalleDescuentoEntity $model)
    {
        $fechaActual = Carbon::now('America/Argentina/Buenos_Aires');
        AuditarFacturacionEntity::create([
            'nombre_tabla' => "tb_facturacion_detalle_descuento",
            'id_tabla' => $model->id_detalle_descuento,
            'data_anterior' => json_encode($model->getOriginal()),
            'data_nueva' => json_encode($model->getChanges()),
            'cod_usuario' => auth()->id(),
            'tipo_accion' => 'UPDATE',
            'fecha_accion' => $fechaActual
        ]);
    }


    public function deleted(FacturacionDetalleDescuentoEntity $model)
    {
        $fechaActual = Carbon::now('America/Argentina/Buenos_Aires');
        AuditarFacturacionEntity::create([
            'nombre_tabla' => "tb_facturacion_detalle_descuento",
            'id_tabla' => $model->id_detalle_descuento,
            'data_anterior' => json_encode($model->getOriginal()),
            'data_nueva' => null,
            'cod_usuario' => auth()->id(),
            'tipo_accion' => 'DELETE',
            'fecha_accion' => $fechaActual
        ]);
    }


    public function restored(FacturacionDetalleDescuentoEntity $facturacionDetalleDescuentoEntity)
    {
        //
    }


    public function forceDeleted(FacturacionDetalleDescuentoEntity $facturacionDetalleDescuentoEntity)
    {
        //
    }
}
