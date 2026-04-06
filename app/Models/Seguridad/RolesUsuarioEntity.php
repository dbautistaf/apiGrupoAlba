<?php

namespace App\Models\Seguridad;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RolesUsuarioEntity extends Model
{
    use HasFactory;
    protected $table = 'tb_roles_usuario';
    protected $primaryKey = 'cod_roles';
    public $timestamps = false;

    protected $fillable = [
        'cod_permisos',
        'cod_usuario'
    ];
}
