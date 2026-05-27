<?php

namespace App\Models\Internaciones;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RecienNacidoEntity extends Model
{
     use HasFactory;
    protected $table = 'tb_recien_nacido';
    protected $primaryKey = 'cod_recien_nacido';
    public $timestamps = false;
    
    protected $fillable = [
        'dni_rn',
        'nombre_rn',
        'apellidos_rn',
        'fecha_nacimiento',
        'diagnostico',
        'observaciones',
        'cod_internacion',
        'fecha_registra',
        'cod_usuario',
    ];

    public function autorizacion()
    {
        return $this->hasMany(AutorizacionRecienNacidoEntity::class, 'cod_recien_nacido', 'cod_recien_nacido');
    }
}
