<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PracticasDiscaPacidadModel extends Model
{
    use HasFactory;
    protected $table = 'tb_practicas_discapacidad';
    protected $primaryKey = 'id_practica';
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'id_practica',
        'modulo',
        'dependencia'
    ];
}
