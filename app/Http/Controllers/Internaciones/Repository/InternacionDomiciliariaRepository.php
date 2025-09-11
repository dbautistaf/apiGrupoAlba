<?php

namespace App\Http\Controllers\Internaciones\Repository;

use App\Models\Internaciones\InternacionDomiciliariaDetalleEntity;
use App\Models\Internaciones\InternacionDomiciliariaEntity;
use App\Models\Internaciones\InternacionDomiciliariaFileEntity;
use App\Models\Internaciones\InternacionDomiciliariaServiciosEntity;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class InternacionDomiciliariaRepository
{
    private $user;
    private $fechaActual;

    public function __construct()
    {
        $this->user = Auth::user();
        $this->fechaActual = Carbon::now('America/Argentina/Buenos_Aires');
    }

    public function findByAddService($params)
    {
        return InternacionDomiciliariaServiciosEntity::create([
            'tipo_servicio' => $params->tipo_servicio,
            'frecuencia' => $params->frecuencia,
            'duracion' => $params->duracion,
            'costo_unitario' => $params->costo_unitario,
        ]);
    }

    public function findByUpdateService($params)
    {
        $servicio = InternacionDomiciliariaServiciosEntity::find($params->id_servicio);
        $servicio->tipo_servicio = $params->tipo_servicio;
        $servicio->frecuencia = $params->frecuencia;
        $servicio->duracion = $params->duracion;
        $servicio->costo_unitario = $params->costo_unitario;
        $servicio->update();

        return $servicio;
    }

    public function findByDeleteServiceId($id)
    {
        $servicio = InternacionDomiciliariaServiciosEntity::find($id);
        return $servicio->delete();
    }

    public function findByUpdateEstadoId($id, $estado)
    {
        $servicio = InternacionDomiciliariaEntity::find($id);
        $servicio->id_tipo_estado = $estado;
        return $servicio->update();
    }

    public function findBySaveInternacionDomiciliaria($params)
    {
        return InternacionDomiciliariaEntity::create([
            'dni_afiliado' => $params->dni_afiliado,
            'edad_afiliado' => $params->edad_afiliado,
            'fecha_solicitud' => $params->fecha_solicitud,
            'solicitante' => $params->solicitante,
            'observaciones' => $params->observaciones,
            'id_tipo_estado' => 1,
            'diagnostico_medico' => $params->diagnostico_medico,
            'cod_usuario' => $this->user->cod_usuario
        ]);
    }

    public function findByUpdateInternacionDomiciliaria($params)
    {
        $inter = InternacionDomiciliariaEntity::find($params->id_internacion_domiciliaria);
        $inter->dni_afiliado = $params->dni_afiliado;
        $inter->edad_afiliado = $params->edad_afiliado;
        $inter->fecha_solicitud = $params->fecha_solicitud;
        $inter->solicitante = $params->solicitante;
        $inter->observaciones = $params->observaciones;
        $inter->id_tipo_estado = $params->id_tipo_estado;
        $inter->diagnostico_medico = $params->diagnostico_medico;
        $inter->update();
        return $inter;
    }

    public function findByInternacionDomiciliariaDetalle($detalle, $internacion)
    {
        foreach ($detalle as $key) {
            $id_servicio = $key->id_servicio;
            if ($key->id_servicio=='') {
                $serviceDB =   $this->findByAddService((object) $key);
                $id_servicio = $serviceDB->id_servicio;
                $key->id_servicio = $id_servicio;
            } else {
                $this->findByUpdateService((object) $key);
            }

            if (InternacionDomiciliariaDetalleEntity::where('id_servicio', $id_servicio)
                ->where('id_internacion_domiciliaria', $internacion->id_internacion_domiciliaria)
                ->exists()
            ) {
                $this->findByUpdateInternacionDomiciliariaDetalleItem((object) $key);
            } else {
                $this->findByAddInternacionDomiciliariaDetalle((object) $key, $internacion);
            }
        }
    }

    public function findByAddInternacionDomiciliariaDetalle($key, $internacion)
    {
        InternacionDomiciliariaDetalleEntity::create([
            'id_servicio' => $key->id_servicio,
            'cantidad' => $key->cantidad,
            'observaciones' => $key->cantidad,
            'id_internacion_domiciliaria' => $internacion->id_internacion_domiciliaria
        ]);
    }

    public function findByUpdateInternacionDomiciliariaDetalleItem($row)
    {
        $item = InternacionDomiciliariaDetalleEntity::find($row->id_detalle);
        $item->id_servicio = $row->id_servicio;
        $item->cantidad = $row->cantidad;
        $item->observaciones = $row->observaciones;
        $item->update();
    }

    public function findByDeleteInternacionDomiciliariaDetalleId($id)
    {
        InternacionDomiciliariaDetalleEntity::where('id_internacion_domiciliaria', $id)->delete();
        return InternacionDomiciliariaDetalleEntity::find($id)->delete();
    }

    public function findBySaveInternacionDomiciliariaFile($archivos, $id_Internacion)
    {
        foreach ($archivos as $key) {
            InternacionDomiciliariaFileEntity::create([
                'archivo' => $key['nombre'],
                'fecha_carga' => $this->fechaActual,
                'id_internacion_domiciliaria' => $id_Internacion
            ]);
        }
    }

    public function findByFinalizarId($params){
        $inter = InternacionDomiciliariaEntity::find($params->id);
        $inter->id_tipo_estado = 6;
        $inter->observacion_final = $params->observacion_final;
       return $inter->update();
    }

    public function findByObtenerAdjuntoId($id)
    {
        return InternacionDomiciliariaFileEntity::find($id);
    }
}
