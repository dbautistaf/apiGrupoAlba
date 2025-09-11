<?php

namespace App\Models\medicos;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EspecialidadesMedicasEntity extends Model
{
    use HasFactory;
    protected $table = 'tb_especialidades';
    protected $primaryKey = 'id_especialidad';
    public $timestamps = false;

    protected $fillable = [
        'especialidad',
        'intervalo',
        'activo',
        'id_centro_medico'
    ];

    public function centromedico()
    {
        return $this->hasOne(CentrosMedicosEntity::class, 'id_centro_medico', 'id_centro_medico');
    }
}
