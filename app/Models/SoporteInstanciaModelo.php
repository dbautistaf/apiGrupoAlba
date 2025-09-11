<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SoporteInstanciaModelo extends Model
{
    use HasFactory;
    protected $connection = 'mysql_soporte';
    protected $table = 'tb_instancias';
    protected $primaryKey = 'id_instancia';
    public $timestamps = false;

    protected $fillable = [
        'instancia_tipo'
    ];
}
