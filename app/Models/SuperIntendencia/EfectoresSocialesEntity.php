<?php

namespace  App\Models\SuperIntendencia;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EfectoresSocialesEntity extends Model
{
    use HasFactory;
    protected $table = 'tb_efectores_sociales';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'cuit_titular',
        'obra_social',
        'nombres_efector',
        'calle',
        'numero',
        'piso',
        'departamento',
        'localidad',
        'codigo_postal',
        'provincia',
        'periodo_importacion',
        'id_usuario',
        'fecha_importacion'
    ];
}
