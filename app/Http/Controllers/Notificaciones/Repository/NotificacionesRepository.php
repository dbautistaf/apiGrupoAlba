<?php

namespace App\Http\Controllers\Notificaciones\Repository;

use App\Models\Notificaciones\NotificacionesEntity;
use Illuminate\Support\Carbon;

class NotificacionesRepository
{

    public function findByCreate($params)
    {
        $user = auth()->user();
        NotificacionesEntity::create([
            'modulo' => $params->modulo,
            'prioridad' => $params->prioridad,
            'fecha_notifica' => Carbon::now(),
            'mensaje' => $params->mensaje,
            'estado_informa' => $params->estado_informa,
            'estado_notifica' => 'PENDIENTE',
            'email_notificar' => $user->email,
            'usuario_notificar' => $params->usuario_notificar,
            'cod_usuario_responsable' =>  $user->cod_usuario,
            'observaciones' => $params->observaciones
        ]);
    }

    public function findByListar($user)
    {
        return NotificacionesEntity::where('usuario_notificar', $user)->get();
    }
}
