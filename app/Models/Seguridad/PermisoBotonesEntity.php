<?php

namespace App\Models\Seguridad;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PermisoBotonesEntity extends Model
{
    use HasFactory;
    protected $table = 'tb_permiso_roles';
    protected $primaryKey = 'cod_permisos';
    public $timestamps = false;

    protected $fillable = [
        'descripcion',
        'validar_btn',
        'cod_menu',
        'estado'
    ];
}
