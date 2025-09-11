<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DesempleoModelo extends Model
{
    use HasFactory;
    protected $table = 'tb_desempleo';
    protected $primaryKey = 'id';
    public $timestamps = false;
    protected $fillable = [
        'clave_desempleo',
        'marca_fin_pago',
        'parentesco',
        'tipo_documento',
        'nro_documento',
        'provincia',
        'cuil',
        'fecha_nacimiento',
        'nombres',
        'fecha_vigencia',
        'sexo',
        'fecha_inicio_relacion',
        'fecha_cese',
        'rnos',
        'fecha_proceso',
        'cuil_titular',
        'periodo_importacion',
        'id_usuario',
        'fecha_importacion'
    ];
}
