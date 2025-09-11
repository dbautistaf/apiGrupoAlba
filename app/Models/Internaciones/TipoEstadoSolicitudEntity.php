<?php

namespace   App\Models\Internaciones;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TipoEstadoSolicitudEntity extends Model
{
    use HasFactory;

    protected $table = 'tb_internaciones_tipo_estado_solicitud';
    protected $primaryKey = 'id_tipo_estado';
    public $timestamps = false;

    protected $fillable = [
        'estado',
        'class_name'
    ];
}
