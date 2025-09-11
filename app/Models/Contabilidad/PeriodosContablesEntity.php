<?php

namespace App\Models\Contabilidad;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PeriodosContablesEntity extends Model
{
    use HasFactory;
    protected $table = 'tb_cont_periodos_contables';
    protected $primaryKey = 'id_periodo_contable';
    public $timestamps = false;

    protected $fillable = [
        'anio_periodo',
        'periodo_contable',
        'periodo_inicio',
        'periodo_fin',
        'cod_usuario_crea',
        'fecha_registra',
        'cod_usuario_modifica',
        'fecha_modifica',
        'vigente',
        'activo'
    ];
}
