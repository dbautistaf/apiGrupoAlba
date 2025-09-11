<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TopesPlanesAfiliadoEntity extends Model
{
    use HasFactory;

    protected $table = 'tb_topes_planes_afiliado';
    protected $primaryKey = 'cod_tope';
    public $timestamps = false;

    protected $fillable = [
        'tope_mensual',
        'tope_anual',
        'vigente',
        'cod_tipo_plan_afiliado',
        'cod_practica_grupo'
    ];
}
