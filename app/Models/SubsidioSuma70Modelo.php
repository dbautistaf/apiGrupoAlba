<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubsidioSuma70Modelo extends Model
{
    use HasFactory;
    protected $table = 'tb_subsidio_suma70';
    protected $primaryKey = 'id_subsidio_suma70';
    public $timestamps = false;

    protected $fillable = [
        'cod_obra_soc',
        'periodo',
        'cant_benef',
        'area_reser',
        'subsidio_total',
        'periodo_subsidio_suma70',
        'fecha_proceso',
        'id_usuario',
    ];
}
