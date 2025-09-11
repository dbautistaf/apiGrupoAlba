<?php

namespace App\Models\RecetarioMedicacion;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class  VademecumRecetarioMedicacionEntity extends Model
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
