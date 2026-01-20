<?php

namespace App\Models\Seguridad;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RecuperarContraseniaEntity extends Model
{
    use HasFactory;
    protected $table = 'tb_usuario_recupera_contrasenia';
    protected $primaryKey = 'id_recupera';
    public $timestamps = false;

    protected $fillable = [
        'fecha_solicita',
        'codigo_verificador',
        'email',
        'jwt',
        'ip_equipo',
        'navegador',
        'hosname',
        'fecha_cambio_clave'
    ];
}
