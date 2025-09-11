<?php

namespace  App\Models\SuperIntendencia;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BajaAutomaticaAfipEntity extends Model
{
    use HasFactory;
    protected $table = 'tb_baja_automatica_afip';
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
        'cp',
        'provincia',
        'categoria',
        'periodo_import',
        'id_usuario',
        'fecha_importacion',
    ];
}
