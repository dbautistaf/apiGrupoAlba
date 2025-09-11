<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BajasRegimenGeneralModelo extends Model
{
    use HasFactory;
    protected $table = 'tb_bajas_regimen_general';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'cuil_titular', 
        'nombres',
        'fecha_vigencia',
        'telefono',
        'email',
        'codigo_postal',
        'localidad',
        'provincia',
        'obra_social_elegida',
        'periodo',
        'id_usuario',
        'fecha_importacion',
    ];
}
