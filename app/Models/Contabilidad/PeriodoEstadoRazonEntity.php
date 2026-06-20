<?php

namespace App\Models\Contabilidad;

use Illuminate\Database\Eloquent\Model;

class PeriodoEstadoRazonEntity extends Model
{
    protected $table = 'tb_cont_periodo_estado_razon';
    protected $primaryKey = 'id_periodo_estado_razon';
    public $timestamps = false;

    protected $fillable = [
        'id_periodo_contable',
        'id_razon',
        'activo',
        'vigente',
        'cod_usuario',
        'fecha_registra',
        'cod_usuario_modifica',
        'fecha_modifica',
    ];

    public function periodoContable()
    {
        return $this->belongsTo(PeriodosContablesEntity::class, 'id_periodo_contable', 'id_periodo_contable');
    }
}
