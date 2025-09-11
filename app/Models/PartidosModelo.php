<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PartidosModelo extends Model
{
    use HasFactory;
    protected $table = 'tb_partidos';
    protected $primaryKey = 'id_partido';
    public $timestamps = false;

    protected $fillable = [
        'id_provincia',
        'nombre',
        'codigo_indec'
    ];
}
