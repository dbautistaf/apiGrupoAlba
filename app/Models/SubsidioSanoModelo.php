<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubsidioSanoModelo extends Model
{
    use HasFactory;
    protected $table = 'tb_subsidio_sano';
    protected $primaryKey = 'id_subsidio_sano';
    public $timestamps = false;
    protected $fillable = [
        'tipo_reg',
        'cuit',
        'cuil',
        'codosoc',
        'periodo',
        'remosimp',
        'apobsoc',
        'conosoc',
        'subsidio',
        'obsocrel',
        'inpartot',
        'inddbcr',
        'motoexcep',
        'periodo_subsidio_sano',
        'fecha_proceso',
        'id_usuario'
    ];
}
