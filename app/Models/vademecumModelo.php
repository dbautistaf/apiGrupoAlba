<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class vademecumModelo extends Model
{
    use HasFactory;
    protected $table = 'tb_vademecum';
    protected $primaryKey = 'id_vademecum';    
    public $timestamps = false;

    protected $fillable = [
        'troquel',
        'registro',
        'nombre',
        'presentacion',
        'laboratorio',
        'droga',
        'accion',
        'acargo_ospf',
        'autorizacion_previa',
        'activo'
    ];
}
