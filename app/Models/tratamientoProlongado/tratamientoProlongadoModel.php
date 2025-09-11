<?php

namespace App\Models\tratamientoProlongado;

use App\Models\afiliado\AfiliadoPadronEntity;
use App\Models\EspecialidadesMedicasEntity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class tratamientoProlongadoModel extends Model
{
    use HasFactory;
    protected $table = 'tb_tratamiento_prolongado';
    protected $primaryKey = 'id_tratamiento';
    public $timestamps = false;

    protected $fillable = [
        'dni_afiliado',
        'edad',
        'nro_ingreso',
        'fecha_proceso',
        'nombres_medico',
        'especialidad_medico',
        'telefono_medico',
        'email_medico',
        'fecha_inicio_tratamiento',
        'fecha_fin_tratamiento',
        'id_usuario',
        'observaciones'
    ];

    public function afiliado()
    {
        return $this->hasOne(AfiliadoPadronEntity::class, 'dni', 'dni_afiliado');
    }

    public function detalles()
    {
        return $this->hasMany(detalleTratamientoModel::class, 'id_tratamiento', 'id_tratamiento');
    }

    public function especialidad()
    {
        return $this->hasOne(EspecialidadesMedicasEntity::class, 'cod_especialidad', 'especialidad_medico');
    }
}
