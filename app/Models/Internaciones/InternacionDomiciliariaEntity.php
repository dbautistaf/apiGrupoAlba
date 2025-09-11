<?php

namespace  App\Models\Internaciones;

use App\Models\afiliado\AfiliadoPadronEntity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InternacionDomiciliariaEntity extends Model
{
    use HasFactory;
    protected $table = 'tb_internaciones_domiciliaria';
    protected $primaryKey = 'id_internacion_domiciliaria';
    public $timestamps = false;
    protected $fillable = [
        'dni_afiliado',
        'edad_afiliado',
        'fecha_solicitud',
        'solicitante',
        'observaciones',
        'id_tipo_estado',
        'diagnostico_medico',
        'cod_usuario',
        'observacion_final'
    ];
    public function afiliado()
    {
        return $this->hasOne(AfiliadoPadronEntity::class, 'dni', 'dni_afiliado');
    }
    public function estado()
    {
        return $this->hasOne(TipoEstadoSolicitudEntity::class, 'id_tipo_estado', 'id_tipo_estado');
    }

    public function detalle()
    {
        return $this->hasMany(InternacionDomiciliariaDetalleEntity::class, 'id_internacion_domiciliaria', 'id_internacion_domiciliaria');
    }
}
