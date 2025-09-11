<?php

namespace  App\Models\PrestacionesMedicas;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TipoEstadoSolicitudEntity extends Model
{
    use HasFactory;
    protected $table = 'tb_prestaciones_medicas_solictud_lentes_tipo_estado';
    protected $primaryKey = 'id_tipo_estado';
    public $timestamps = false;

    protected $fillable = [
        'estado',
        'class_name',
        'icon_badge'
    ];
}
