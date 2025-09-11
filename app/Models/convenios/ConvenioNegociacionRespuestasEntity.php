<?php

namespace App\Models\convenios;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConvenioNegociacionRespuestasEntity extends Model
{
    use HasFactory;
    protected $table = 'tb_convenios_negociacion_respuestas';
    protected $primaryKey = 'id_negociacion_respuesta';
    public $timestamps = false;

    protected $fillable = [
        'id_negociacion',
        'id_tipo_respuesta',
        'fecha_registra',
        'observaciones'
    ];

    public function tipo()
    {
        return $this->hasOne(ConvenioTipoRespuestaEntity::class, 'id_tipo_respuesta', 'id_tipo_respuesta');
    }
}
