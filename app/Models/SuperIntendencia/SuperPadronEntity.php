<?php

namespace App\Models\SuperIntendencia;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SuperPadronEntity extends Model
{
    use HasFactory;
    protected $table = 'tb_super_padron';
    protected $primaryKey = 'id_registro';
    public $timestamps = false;

    protected $fillable = [
        'rnos',
        'cuit',
        'cuil_tit',
        'parentesco',
        'cuil_benef',
        'tipo_doc',
        'dni',
        'nombres',
        'sexo',
        'estado_civi',
        'fe_nac',
        'nacionalidad',
        'calle',
        'numero',
        'piso',
        'depto',
        'localidad',
        'cp',
        'id_prov',
        'sd2',
        'telefono',
        'sd3',
        'incapacidad',
        'sd5',
        'fe_alta',
        'fe_novedad',
        'periodo',
        'id_usuario',
        'fecha_importacion',
    ];
}
