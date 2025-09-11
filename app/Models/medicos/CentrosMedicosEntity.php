<?php

namespace App\Models\medicos;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CentrosMedicosEntity extends Model
{
    use HasFactory;
    protected $table = 'tb_centros_medicos';
    protected $primaryKey = 'id_centro_medico';
    public $timestamps = false;

    protected $fillable = [
        'nombre',
        'fecha_alta',
        'fecha_baja',
        'observaciones',
        'responsable',
        'email',
        'celular',
        'telefono',
        'activo'
    ];
}
