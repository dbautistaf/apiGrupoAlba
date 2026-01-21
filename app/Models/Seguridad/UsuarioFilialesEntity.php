<?php

namespace App\Models\Seguridad;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UsuarioFilialesEntity extends Model
{
    use HasFactory;
    protected $table = 'tb_usuarios_filiales';
    public $timestamps = false;

    protected $fillable = [
        'cod_usuario',
        'cod_perfil',
    ];
}
