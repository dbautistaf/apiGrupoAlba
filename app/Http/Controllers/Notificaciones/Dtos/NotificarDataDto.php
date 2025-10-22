<?php

namespace App\Http\Controllers\Notificaciones\Dtos;

class NotificarDataDto
{
    public $modulo;
    public $prioridad;
    public $mensaje;
    public $usuario_notificar;
    public $estado_informa;
    public $observaciones;
    public function __construct($modulo,  $prioridad,  $mensaje,  $usuario_notificar,  $estado_informa,  $observaciones)
    {
        $this->modulo = $modulo;
        $this->prioridad = $prioridad;
        $this->mensaje = $mensaje;
        $this->usuario_notificar = $usuario_notificar;
        $this->estado_informa = $estado_informa;
        $this->observaciones = $observaciones;
    }
}
