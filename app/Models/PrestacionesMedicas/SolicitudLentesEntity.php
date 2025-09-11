<?php

namespace App\Models\PrestacionesMedicas;

use App\Models\afiliado\AfiliadoPadronEntity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SolicitudLentesEntity extends Model
{
    use HasFactory;
    protected $table = 'tb_lentes_solicitudes';
    protected $primaryKey = 'id_solitud_lente';
    public $timestamps = false;

    protected $fillable = [
        'dni_afiliado',
        'edad_afiliado',
        'fecha_solicitud',
        'solicitante',
        'descripcion_receta',
        'id_tipo_estado',
        'cod_usuario',
        'descripcion_armazon',
        'descripcion_material',
        'fecha_entrega',
        'observaciones_entrega',
        'cod_usuario_entrega'
    ];

    public function estado()
    {
        return $this->hasOne(TipoEstadoLentesEntity::class, 'id_tipo_estado', 'id_tipo_estado');
    }

    public function afiliado()
    {
        return $this->hasOne(AfiliadoPadronEntity::class, 'dni', 'dni_afiliado');
    }
}
