<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActividadEconomicaModelo extends Model
{
    use HasFactory;

    protected $table = 'tb_actividad_economica';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'codigo_afip',
        'descripcion',
        'nombre',
    ];
}
