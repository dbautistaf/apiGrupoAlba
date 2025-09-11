<?php

namespace App\Models\prestadores;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TipoMatriculaMedicosEntity extends Model
{
    use HasFactory;
    protected $table = 'tb_tipo_matricula_profesional';
    protected $primaryKey = 'cod_tipo_matricula';
    public $timestamps = false;

    protected $fillable = [
        'descripcion_matricula',
        'vigente'
    ];
}
