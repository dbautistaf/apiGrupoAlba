<?php

namespace App\Models\prestadores;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PrestadorEspecialidadesMedicasEntity extends Model
{
    use HasFactory;
    protected $table = 'tb_especialidades_medicas';
    protected $primaryKey = 'cod_especialidad';
    public $timestamps = false;

    protected $fillable = [
        'descripcion_especialidad',
        'vigente'
    ];
}
