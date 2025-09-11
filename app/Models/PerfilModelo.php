<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PerfilModelo extends Model
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
