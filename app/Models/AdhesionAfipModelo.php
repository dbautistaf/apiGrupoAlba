<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdhesionAfipModelo extends Model
{
    use HasFactory;
    protected $table = 'tb_adhesion_afip';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'cuil_tit',
        'rnos',
        'periodo',
        'cuit',
        'nombres',
        'calle',
        'numero',
        'piso',
        'depto',
        'localidad',
        'periodo_import',
        'id_usuario',
        'fecha_importacion'
    ];
}
