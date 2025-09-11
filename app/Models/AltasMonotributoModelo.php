<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AltasMonotributoModelo extends Model
{
    use HasFactory;
    protected $table = 'tb_altas_monotributo';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'tipo',
        'formulario', 
        'cuil', 
        'nombres',
        'periodo_vigencia',
        'telefono', 
        'email',
        'codigo_postal', 
        'localidad',
        'provincia',
        'obra_social_origen', 
        'periodo',
        'id_usuario',
        'fecha_importacion',
    ];
}
