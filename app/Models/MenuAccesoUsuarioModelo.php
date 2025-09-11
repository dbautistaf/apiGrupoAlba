<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MenuAccesoUsuarioModelo extends Model
{
    use HasFactory;

    protected $table = 'tb_menu_acceso_usuario';
    protected $primaryKey = 'cod_acceso';
    public $timestamps = false;

    protected $fillable = [
        'cod_menu',
        'cod_perfil',
        'estado_acceso'
    ];

    public function menu()
    {
        return $this->hasOne(MenuModelo::class, 'cod_menu', 'cod_menu');
    }

    public function perfil()
    {
        return $this->hasOne(PerfilModelo::class, 'cod_perfil', 'cod_perfil');
    }
}
