<?php

namespace App\Models\facturacion;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FacturacionDetalleDescuentoEntity extends Model
{
    use HasFactory;
    protected $table = 'tb_facturacion_detalle_descuento';
    protected $primaryKey = 'id_detalle_descuento';
    public $timestamps = false;

    protected $fillable = [
        'descuento',
        'importe',
        'observaciones',
        'id_factura'
    ];
}
