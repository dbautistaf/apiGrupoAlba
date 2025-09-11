<?php

namespace App\Models\liquidaciones;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LiqMedicamentosEntity extends Model
{
    use HasFactory;

    protected $table = 'tb_liquidaciones_medicamentos';
    protected $primaryKey = 'id_liquidacion';
    public $timestamps = false;

    protected $fillable = [
        'id_factura',
        'id_afiliado',
        'edad_afiliado',
        'id_cobertura',
        'id_tipo_iva',
        'cod_profesional',
        'cod_provincia',
        'diagnostico',
        'observaciones_debito',
        'observaciones',
        'fecha_venta',
        'fecha_prescripcion',
        'referencia',
        'fecha_registra',
        'cod_usuario',
        'fecha_actualiza',
        'total_facturado',
        'total_aprobado',
        'total_debitado'
    ];
}
