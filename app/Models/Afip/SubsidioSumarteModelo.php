<?php

namespace  App\Models\Afip;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubsidioSumarteModelo extends Model
{
    use HasFactory;
    protected $table = 'tb_subsidio_sumarte';
    protected $primaryKey = 'id_subsidio_sumarte';
    public $timestamps = false;

    protected $fillable = [
        'cod_obra_soc',
        'periodo',
        'cant_benef',
        'area_reser',
        'subsidio_total',
        'periodo_subsidio_sumarte',
        'fecha_proceso',
        'id_usuario',
    ];
}
