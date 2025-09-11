<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LocalidadModelo extends Model
{
    use HasFactory;
    protected $table = 'tb_localidad';
    protected $primaryKey = 'id_localidad';
    public $timestamps = false;

    protected $fillable = [
        'id_provincia',
        'id_partido',
        'nombre',
        'municipio',
        'id_cpostal',
        'latitud',
        'longitud'
    ];
}
