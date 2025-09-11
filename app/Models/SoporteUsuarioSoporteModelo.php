<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SoporteUsuarioSoporteModelo extends Model
{
    use HasFactory;
    protected $connection = 'mysql_soporte';
    protected $table = 'tb_usuario_soporte';
    protected $primaryKey = 'id_encargado';
    public $timestamps = false;

    protected $fillable = [
        'encargado'
    ];
}
