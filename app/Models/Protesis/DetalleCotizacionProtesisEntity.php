<?php

namespace App\Models\Protesis;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetalleCotizacionProtesisEntity extends Model
{

    use HasFactory;
    protected $table = 'tb_protesis_detalle_cotizacion';
    protected $primaryKey = 'id_cotizacion';
    public $timestamps = false;

    protected $fillable = [
        'id_detalle_producto_licitacion',
        'cantidad_autorizada',
        'monto_cotiza',
        'importe_total',
        'observaciones',
        'id_solicitud',
        'fecha_registra',
        'id_protesis',
        'cod_usuario'
    ];
}
