<?php

namespace App\Models\afiliado;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TipoProgramaEspecialAfiEntity extends Model
{
    use HasFactory;
    protected $table = 'tb_afiliado_tipo_programa_especial';
    protected $primaryKey = 'id_tipo_programa_especial';
    public $timestamps = false;

    protected $fillable = [
        'descripcion_programa',
        'vigente'
    ];
}
