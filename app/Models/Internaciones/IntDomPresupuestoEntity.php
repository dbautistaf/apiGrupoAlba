<?php

namespace App\Models\Internaciones;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IntDomPresupuestoEntity extends Model
{
    use HasFactory;
    protected $table = 'tb_internaciones_domiciliaria_presupuesto';
    protected $primaryKey = 'id_cotizacion';
    public $timestamps = false;
    protected $fillable = [
        'id_detalle',
        'cantidad_autorizada',
        'monto_cotiza',
        'importe_total',
        'observaciones',
        'id_solicitud',
        'fecha_registra',
        'cod_usuario'
    ];
}
