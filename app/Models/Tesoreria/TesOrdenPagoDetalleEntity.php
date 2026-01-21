<?php

namespace App\Models\Tesoreria;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TesOrdenPagoDetalleEntity extends Model
{
    use HasFactory;
    protected $table = 'tb_tes_orden_pago_detalle';
    protected $primaryKey = 'id_orden_pago_detalle';
    public $timestamps = false;

    protected $fillable = [
        'id_orden_pago',
        'id_factura',
        'monto_factura',
        'tipo_factura'
    ];
}
