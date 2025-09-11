<?php

namespace App\Models\liquidaciones;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LiquidacionEntity extends Model
{
    use HasFactory;

    protected $table = 'tb_liquidaciones';
    protected $primaryKey = 'id_liquidacion';
    public $timestamps = false;

    protected $fillable = [
        'id_factura',
        'num_lote',
        'id_afiliado',
        'edad_afiliado',
        'id_cobertura',
        'id_tipo_iva',
        'cod_profesional',
        'cod_provincia',
        'diagnostico',
        'observaciones',
        'fecha_registra',
        'cod_usuario',
        'fecha_actualiza',
        'total_facturado',
        'total_aprobado',
        'total_debitado',
        'dni_afiliado',
        'total_coseguro'
    ];
}
