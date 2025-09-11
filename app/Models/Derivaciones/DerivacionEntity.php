<?php

namespace App\Models\Derivaciones;

use App\Models\afiliado\AfiliadoPadronEntity;
use App\Models\LocatorioModelos;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DerivacionEntity extends Model
{
    use HasFactory;
    protected $table = 'tb_derivacion';
    protected $primaryKey = 'id_derivacion';
    public $timestamps = false;

    protected $fillable = [
        'sexo',
        'acompaniante',
        'id_tipo_paciente',
        'id_tipo_derivacion',
        'id_locatorio',
        'dni_afiliado',
        'edad_afiliado',
        'diagnostico',
        'id_tipo_sector',
        'fecha_solicitud',
        'fecha_traslado',
        'hora_solicitud',
        'hora_traslado',
        'hora_destino',
        'id_tipo_egreso',
        'dias_internacion',
        'gasto_total',
        'gasto_extra',
        'observaciones',
        'diagnostico_final',
        'id_derivacion_medico',
        'id_tipo_estado',
        'cod_usuario',
        'estado_presupuesto'
    ];

    public function medico()
    {
        return $this->hasOne(DerivacionDatosMedicosEntity::class, 'id_derivacion_medico', 'id_derivacion_medico');
    }

    public function afiliado()
    {
        return $this->hasOne(AfiliadoPadronEntity::class, 'dni', 'dni_afiliado');
    }

    public function estado()
    {
        return $this->hasOne(TipoEstadoDerivacionEntity::class, 'id_tipo_estado', 'id_tipo_estado');
    }

    public function obrasocial()
    {
        return $this->hasOne(LocatorioModelos::class,  'id_locatorio', 'id_locatorio');
    }

    public function tipoPaciente()
    {
        return $this->hasOne(TipoPacienteEntity::class,  'id_tipo_paciente', 'id_tipo_paciente');
    }

    public function tipoDerivacion()
    {
        return $this->hasOne(TipoDerivacionEntity::class,  'id_tipo_derivacion', 'id_tipo_derivacion');
    }

    public function tipoSector()
    {
        return $this->hasOne(TipoSectorEntity::class,  'id_tipo_sector', 'id_tipo_sector');
    }

    public function autorizacion()
    {
        return $this->hasOne(AutorizacionesDerivacionEntity::class,  'id_derivacion', 'id_derivacion');
    }
}
