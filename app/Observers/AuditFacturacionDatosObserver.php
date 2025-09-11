<?php

namespace App\Observers;
use Carbon\Carbon;
use App\Models\auditoria\AuditarFacturacionEntity;
use App\Models\facturacion\FacturacionDatosEntity;

class AuditFacturacionDatosObserver
{

    public function created(FacturacionDatosEntity $model)
    {
        $fechaActual = Carbon::now('America/Argentina/Buenos_Aires');
        AuditarFacturacionEntity::create([
            'nombre_tabla' => "tb_facturacion_datos",
            'id_tabla' => $model->id_factura,
            'data_anterior' => null,
            'data_nueva' => json_encode($model->getAttributes()),
            'cod_usuario' => auth()->id(),
            'tipo_accion' => 'CREATE',
            'fecha_accion' => $fechaActual
        ]);
    }


    public function updated(FacturacionDatosEntity $model)
    {
        $fechaActual = Carbon::now('America/Argentina/Buenos_Aires');
        AuditarFacturacionEntity::create([
            'nombre_tabla' => "tb_facturacion_datos",
            'id_tabla' => $model->id_factura,
            'data_anterior' => json_encode($model->getOriginal()),
            'data_nueva' => json_encode($model->getChanges()),
            'cod_usuario' => auth()->id(),
            'tipo_accion' => 'UPDATE',
            'fecha_accion' => $fechaActual
        ]);
    }


    public function deleted(FacturacionDatosEntity $model)
    {
        $fechaActual = Carbon::now('America/Argentina/Buenos_Aires');
        AuditarFacturacionEntity::create([
            'nombre_tabla' => "tb_facturacion_datos",
            'id_tabla' => $model->id_factura,
            'data_anterior' => json_encode($model->getOriginal()),
            'data_nueva' => null,
            'cod_usuario' => auth()->id(),
            'tipo_accion' => 'DELETE',
            'fecha_accion' => $fechaActual
        ]);
    }


    public function restored(FacturacionDatosEntity $facturacionDatosEntity)
    {
        //
    }


    public function forceDeleted(FacturacionDatosEntity $facturacionDatosEntity)
    {
        //
    }
}
