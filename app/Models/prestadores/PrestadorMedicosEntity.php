<?php

namespace App\Models\prestadores;

use App\Models\prestadores\TipoMatriculaMedicosEntity;
use App\Models\prestadores\PrestadorEntity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PrestadorMedicosEntity extends Model
{
    use HasFactory;
    protected $table = 'tb_profesionales_prestador';
    protected $primaryKey = 'cod_profesional';
    public $timestamps = false;

    protected $fillable = [
        'dni',
        'apellidos_nombres',
        'numero_matricula',
        'vigente',
        'cod_tipo_matricula',
        'cod_especialidad',
        'cod_prestador',
        'cod_usuario_registra',
        'fecha_alta',
        'fecha_baja',
        'direccion'
    ];

    public function tipoMatricula()
    {
        return $this->hasOne(TipoMatriculaMedicosEntity::class, 'cod_tipo_matricula', 'cod_tipo_matricula');
    }

    public function especialidad()
    {
        return $this->hasOne(PrestadorEspecialidadesMedicasEntity::class, 'cod_especialidad', 'cod_especialidad');
    }

    public function prestador()
    {
        return $this->hasOne(PrestadorEntity::class, 'cod_prestador', 'cod_prestador');
    }
}
