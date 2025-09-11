<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubsidioSumaModelo extends Model
{
    use HasFactory;
    protected $table = 'tb_subsidio_suma';
    protected $primaryKey = 'id_subsidio_suma';
    public $timestamps = false;

    protected $fillable = [
        'cod_obra_soc',
        'periodo',
        'cant_benef',
        'importe',
        'capita',
        'art2_inca',
        'art2_incb',
        'art2_incc',
        'art3_ajuste',
        'subsidio_total',
        'periodo_subsidio_suma',
        'fecha_proceso',
        'id_usuario'
    ];
}
