<?php

namespace App\Models\prestadores;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TipoInputacionesContablesEntity extends Model
{
    use HasFactory;
    protected $table = 'tb_prestador_tipo_imputacion_contable';
    protected $primaryKey = 'id_tipo_imputacion_contable';
    public $timestamps = false;

    protected $fillable = [
        'imputacion',
        'codigo',
        'vigente',
        'imputable',
        'tipo',
        'imputacion_relacionada',
        'sector_relacionado'
    ];
}
