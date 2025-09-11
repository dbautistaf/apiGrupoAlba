<?php

namespace App\Http\Controllers\PrestacionesMedicas\Repository;

use App\Models\PrestacionesMedicas\SolicitudLentesEntity;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class SolicitudLentesRepository
{

    private $fechaActual;
    private $user;
    public function __construct()
    {
        $this->fechaActual = Carbon::now('America/Argentina/Buenos_Aires');
        $this->user = Auth::user();
    }

    public function findByCreate($params)
    {
        return SolicitudLentesEntity::create([
            'dni_afiliado' => $params->dni_afiliado,
            'edad_afiliado' => $params->edad_afiliado,
            'fecha_solicitud' => $params->fecha_solicitud,
            'solicitante' => $params->solicitante,
            'descripcion_receta' => $params->descripcion_receta,
            'id_tipo_estado' => $params->id_tipo_estado,
            'cod_usuario' => $this->user->cod_usuario,
            'descripcion_armazon' => $params->descripcion_armazon,
            'descripcion_material' => $params->descripcion_material
        ]);
    }

    public function findByUpdate($params)
    {
        $solicitud = SolicitudLentesEntity::find($params->id_solitud_lente);
        $solicitud->dni_afiliado = $params->dni_afiliado;
        $solicitud->edad_afiliado = $params->edad_afiliado;
        $solicitud->fecha_solicitud = $params->fecha_solicitud;
        $solicitud->solicitante = $params->solicitante;
        $solicitud->descripcion_receta = $params->descripcion_receta;
        $solicitud->descripcion_armazon = $params->descripcion_armazon;
        $solicitud->descripcion_material = $params->descripcion_material;
        return $solicitud->update();
    }

    public function findByDeleteId($id)
    {
        $solicitud = SolicitudLentesEntity::find($id);
        return $solicitud->delete();
    }

    public function findByUpdateEstado($id, $estado)
    {
        $solicitud = SolicitudLentesEntity::find($id);
        $solicitud->id_tipo_estado = $estado;
        return $solicitud->update();
    }

    public function findByUpdateEntrega($id, $obs)
    {
        $solicitud = SolicitudLentesEntity::find($id);
        $solicitud->fecha_entrega = $this->fechaActual;
        $solicitud->observaciones_entrega = $obs;
        $solicitud->cod_usuario_entrega = $this->user->cod_usuario;
        return $solicitud->update();
    }
}
