<?php

namespace  App\Models\Notificaciones;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NotificacionesEntity extends Model
{
    use HasFactory;
    protected $table = 'tb_notificaciones';
    protected $primaryKey = 'id_notificacion';
    public $timestamps = false;

    protected $fillable = [
        'modulo',
        'prioridad',
        'fecha_notifica',
        'mensaje',
        'estado_informa',
        'estado_notifica',
        'fecha_lectura',
        'email_notificar',
        'cel_notificar',
        'usuario_notificar',
        'cod_usuario_responsable',
        'cod_usuario_notiifca',
        'observaciones'
    ];
}
