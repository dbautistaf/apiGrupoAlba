<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmpresaModelo extends Model
{
    use HasFactory;
    protected $table = 'tb_empresa';
    protected $primaryKey = 'id_empresa';
    public $timestamps = false;

    protected $fillable = [
        'razon_social',
        'id_localidad',
        'fecha_alta', 
        'fecha_carga',
        'id_usuario',
        'telefono', 
        'celular',  
        'cuit',
        'id_partido',
        'id_provincia',
        'fecha_baja', 
        'nombre_fantasia',
        'email',
        'id_delegacion',
        'id_actividad_economica',
        'observaciones',
        'tipo_empresa',
        'domicilio'
    ];
}
