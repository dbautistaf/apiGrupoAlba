<?php

namespace App\Models\medicos;

use App\Models\afiliado\AfiliadoPadronEntity;
use App\Models\medicos\CentrosMedicosEntity;
use App\Models\medicos\MedicosEntity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TurnosMedicosEntity extends Model
{
    use HasFactory;
    protected $table = 'tb_turnos';
    protected $primaryKey = 'id_turno';
    public $timestamps = false;

    protected $fillable = [
        'fecha_desde',
        'fecha_hasta',
        'horario_inicio',
        'horario_fin',
        'estado',
        'id_afiliado',
        'id_centro_medico',
        'id_medico',
        'id_locatorio',
        'id_especialidad',
        'id_usuario'
    ];

    public function afiliado()
    {
        return $this->hasOne(AfiliadoPadronEntity::class, 'id', 'id_afiliado');
    }

    public function medico()
    {
        return $this->hasOne(MedicosEntity::class, 'id_medico', 'id_medico');
    }

    public function centro()
    {
        return $this->hasOne(CentrosMedicosEntity::class, 'id_centro_medico', 'id_centro_medico');
    }

    public function especialidad()
    {
        return $this->hasOne(EspecialidadesMedicasEntity::class, 'id_especialidad', 'id_especialidad');
    }
}
