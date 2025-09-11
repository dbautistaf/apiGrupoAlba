<?php

namespace App\Http\Controllers\Internaciones\Repository;

use App\Models\Internaciones\AuditorizacionesInternacionEntity;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class AuditarInternacionRepository
{
    private $user;
    private $fechaActual;
    public function __construct()
    {
        $this->user = Auth::user();
        $this->fechaActual = Carbon::now('America/Argentina/Buenos_Aires');
    }


    public function findByAuditar($params)
    {
       return AuditorizacionesInternacionEntity::create([
            'cod_internacion' => $params->cod_internacion,
            'fecha_autoriza' => $params->fecha_autoriza,
            'cod_tipo_estado' => ($params->estado_autoriza ? 1 : 3),
            'cod_usuario' => $this->user->cod_usuario,
            'observaciones' => $params->observacion_rechazo,
            'dias_autoriza' => $params->cantidad_autorizada
        ]);
    }
}
