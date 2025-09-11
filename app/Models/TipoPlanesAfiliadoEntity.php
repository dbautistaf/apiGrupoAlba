<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TipoPlanesAfiliadoEntity extends Model
{
    use HasFactory;
    protected $table = 'tb_tipo_planes_afiliado';
    protected $primaryKey = 'cod_tipo_plan_afiliado';
    public $timestamps = false;

    protected $fillable = [
        'descripcion_corta',
        'descripcio_larga',
        'vigente'
    ];
}
