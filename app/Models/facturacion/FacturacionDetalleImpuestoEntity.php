<?php

namespace App\Models\facturacion;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FacturacionDetalleImpuestoEntity extends Model
{
    use HasFactory;
    protected $table = 'tb_facturacion_detalle_impuesto';
    protected $primaryKey = 'id_detalle_impuesto';
    public $timestamps = false;

    protected $fillable = [
        'impuesto',
        'porcentaje',
        'importe',
        'id_factura',
        'id_tipo_imputacion',
        'is_grupo'
    ];
}
