<?php

namespace App\Models\Seguridad;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PerfilEntity extends Model
{
    use HasFactory;
    protected $table = 'tb_perfiles';
    protected $primaryKey = 'cod_perfil';
    public $timestamps = false;
    protected $fillable = [
        'nombre_perfil',
        'estado'
    ];
}
