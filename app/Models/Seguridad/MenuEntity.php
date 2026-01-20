<?php

namespace App\Models\Seguridad;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MenuEntity extends Model
{
    use HasFactory;
    protected $table = 'tb_menus';
    protected $primaryKey = 'cod_menu';
    public $timestamps = false;

    protected $fillable = [
        'menu_descripcion',
        'menu_icono',
        'menu_link',
        'menu_grupo',
        'menu_principal',
        'menu_orden',
        'menu_estado',
        'tipo_ruta'
    ];
}
