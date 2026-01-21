<?php

namespace App\Models\Seguridad;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MenuAccesoUsuarioEntity extends Model
{
    use HasFactory;

    protected $table = 'tb_menu_acceso_usuario';
    protected $primaryKey = 'cod_acceso';
    public $timestamps = false;

    protected $fillable = [
        'cod_menu',
        'cod_perfil',
        'estado_acceso',
        'estado_escritura',
        'estado_eliminar',
    ];

    public function menu()
    {
        return $this->hasOne(MenuEntity::class, 'cod_menu', 'cod_menu');
    }

    public function perfil()
    {
        return $this->hasOne(PerfilEntity::class, 'cod_perfil', 'cod_perfil');
    }
}
