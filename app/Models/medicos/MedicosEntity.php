<?php

namespace App\Models\medicos;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MedicosEntity extends Model
{
    use HasFactory;
    protected $table = 'tb_medicos';
    protected $primaryKey = 'id_medico';
    public $timestamps = false;

    protected $fillable = [
        'universidad',
        'nombre',
        'cuit',
        'matricula_nacional',
        'matricula_provincial',
        'tipo_matricula',
        'fecha_alta',
        'fecha_baja',
        'email',
        'celular',
        'observaciones',
        'activo',
        'id_especialidad',
        'id_tipo_entidad',
    ];

    public function especialidad()
    {
        return $this->hasOne(EspecialidadesMedicasEntity::class, 'id_especialidad', 'id_especialidad');
    }
}
