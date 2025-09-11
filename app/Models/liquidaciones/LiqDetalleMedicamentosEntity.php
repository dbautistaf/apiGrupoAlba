<?php

namespace App\Models\liquidaciones;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LiqDetalleMedicamentosEntity extends Model
{
    use HasFactory;

    protected $table = 'tb_liquidaciones_detalle_medicamentos';
    protected $primaryKey = 'id_detalle';
    public $timestamps = false;

    protected $fillable = [
        'id_liquidacion',
        'id_medicamento',
        'cantidad',
        'precio_unitario',
        'monto_facturado',
        'cobertura_porcentaje',
        'cargo_os',
        'debita_iva',
        'id_tipo_motivo_debito'
    ];
}
