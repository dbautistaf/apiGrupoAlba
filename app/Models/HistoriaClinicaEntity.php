<?php

namespace App\Models;

use App\Models\afiliado\AfiliadoPadronEntity;
use App\Models\afiliado\AfiliadoTipoDiscapacidad;
use App\Models\prestadores\PrestadorEspecialidadesMedicasEntity;
use App\Models\prestadores\PrestadorMedicosEntity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HistoriaClinicaEntity extends Model
{
    use HasFactory;
    protected $table = 'tb_historia_clinica';
    protected $primaryKey = 'id_historia_clinica';
    public $timestamps = false;

    protected $fillable = [
        'dni_afiliado',
        'peso',
        'talla',
        'imc',
        'frecuencia_cardiaca',
        'presion_arterial',
        'discapacida',
        'alergia',
        'diagnostico',
        'estudios_previos',
        'antecedentes',
        'intolerancias_alimentarias',
        'tratamiento_indicado',
        'observaciones',
        'medicacion_solicitada',
        'fecha_registra',
        'cod_usuario_registra',
        'vigente',
        'cod_tipo_alergia',
        'id_tipo_discapacidad',
        'cod_profesional',
        'cod_especialidad'
    ];

    public function afiliado()
    {
        return $this->hasOne(AfiliadoPadronEntity::class,'dni', 'dni_afiliado');
    }

    public function tipoAlergia()
    {
        return $this->hasOne(TipoAlergiasEntity::class,'cod_tipo_alergia', 'cod_tipo_alergia');
    }

    public function tipoDiscapacidad()
    {
        return $this->hasOne(AfiliadoTipoDiscapacidad::class,'id_tipo_discapacidad', 'id_tipo_discapacidad');
    }


    public function profesional()
    {
        return $this->hasOne(PrestadorMedicosEntity::class,'cod_profesional', 'cod_profesional');
    }

    public function especialidad()
    {
        return $this->hasOne(PrestadorEspecialidadesMedicasEntity::class,'cod_especialidad', 'cod_especialidad');
    }

}
