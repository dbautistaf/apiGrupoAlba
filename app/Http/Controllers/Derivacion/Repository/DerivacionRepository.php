<?php

namespace App\Http\Controllers\Derivacion\Repository;

use App\Models\Derivaciones\AutorizacionesDerivacionEntity;
use App\Models\Derivaciones\DerivacionDatosMedicosEntity;
use App\Models\Derivaciones\DerivacionEntity;
use App\Models\Internaciones\InternacionesEntity;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class DerivacionRepository
{
    private $user;
    private $fechaActual;
    public function __construct()
    {
        $this->user = Auth::user();
        $this->fechaActual = Carbon::now('America/Argentina/Buenos_Aires');
    }

    public function findBySaveDatosMedicos($params)
    {
        return DerivacionDatosMedicosEntity::create([
            'id_tipo_traslado' => $params->id_tipo_traslado,
            'id_tipo_movil' => $params->id_tipo_movil,
            'solicita_por' => $params->solicita_por,
            'medico_solicita' => $params->medico_solicita,
            'entidad_solicitante' => $params->entidad_solicitante,
            'telefono' => $params->telefono,
            'desde_institucion' => $params->desde_institucion,
            'desde_telefono' => $params->desde_telefono,
            'desde_domicilio' => $params->desde_domicilio,
            'desde_localidad' => $params->desde_localidad,
            'hasta_institucion' => $params->hasta_institucion,
            'hasta_telefono' => $params->hasta_telefono,
            'hasta_domicilio' => $params->hasta_domicilio,
            'hasta_localidad' => $params->hasta_localidad,
            'con_regreso' => $params->con_regreso,
            'con_espera' => $params->con_espera,
            'num_internacion' => $params->num_internacion,
            'id_tipo_requisito' => $params->id_tipo_requisito,
            'cant_req_extra' => $params->cant_req_extra,
            'obs_req_extra' => $params->obs_req_extra
        ]);
    }

    public function findByUpdateDatosMedicos($params)
    {
        $medico = DerivacionDatosMedicosEntity::find($params->id_derivacion_medico);
        $medico->id_tipo_traslado = $params->id_tipo_traslado;
        $medico->id_tipo_movil = $params->id_tipo_movil;
        $medico->solicita_por = $params->solicita_por;
        $medico->medico_solicita = $params->medico_solicita;
        $medico->entidad_solicitante = $params->entidad_solicitante;
        $medico->telefono = $params->telefono;
        $medico->desde_institucion = $params->desde_institucion;
        $medico->desde_telefono = $params->desde_telefono;
        $medico->desde_domicilio = $params->desde_domicilio;
        $medico->desde_localidad = $params->desde_localidad;
        $medico->hasta_institucion = $params->hasta_institucion;
        $medico->hasta_telefono = $params->hasta_telefono;
        $medico->hasta_domicilio = $params->hasta_domicilio;
        $medico->hasta_localidad = $params->hasta_localidad;
        $medico->con_regreso = $params->con_regreso;
        $medico->con_espera = $params->con_espera;
        $medico->num_internacion = $params->num_internacion;
        $medico->id_tipo_requisito = $params->id_tipo_requisito;
        $medico->cant_req_extra = $params->cant_req_extra;
        $medico->obs_req_extra = $params->obs_req_extra;
        $medico->update();
        return $medico;
    }

    public function findBySaveDerivacion($params, $medico)
    {
        return DerivacionEntity::create([
            'sexo' => $params->sexo,
            'acompaniante' => $params->acompaniante,
            'id_tipo_paciente' => $params->id_tipo_paciente,
            'id_tipo_derivacion' => $params->id_tipo_derivacion,
            'id_locatorio' => $params->id_locatorio,
            'dni_afiliado' => $params->dni_afiliado,
            'edad_afiliado' => $params->edad_afiliado,
            'diagnostico' => $params->diagnostico,
            'id_tipo_sector' => $params->id_tipo_sector,
            'fecha_solicitud' => $params->fecha_solicitud,
            'fecha_traslado' => $params->fecha_traslado,
            'hora_solicitud' => $params->hora_solicitud,
            'hora_traslado' => $params->hora_traslado,
            'hora_destino' => $params->hora_destino,
            'id_tipo_egreso' => $params->id_tipo_egreso,
            'dias_internacion' => $params->dias_internacion,
            'gasto_total' => $params->gasto_total,
            'gasto_extra' => $params->gasto_extra,
            'observaciones' => $params->observaciones,
            'diagnostico_final' => $params->diagnostico_final,
            'id_derivacion_medico' => $medico->id_derivacion_medico,
            'id_tipo_estado' => 2,
            'cod_usuario' => $this->user->cod_usuario
        ]);
    }

    public function findByUpdateDerivacion($params, $medico)
    {
        $traslado = DerivacionEntity::find($params->id_derivacion);
        $traslado->sexo = $params->sexo;
        $traslado->acompaniante = $params->acompaniante;
        $traslado->id_tipo_paciente = $params->id_tipo_paciente;
        $traslado->id_tipo_derivacion = $params->id_tipo_derivacion;
        $traslado->id_locatorio = $params->id_locatorio;
        $traslado->dni_afiliado = $params->dni_afiliado;
        $traslado->edad_afiliado = $params->edad_afiliado;
        $traslado->diagnostico = $params->diagnostico;
        $traslado->id_tipo_sector = $params->id_tipo_sector;
        $traslado->fecha_solicitud = $params->fecha_solicitud;
        $traslado->fecha_traslado = $params->fecha_traslado;
        $traslado->hora_solicitud = $params->hora_solicitud;
        $traslado->hora_traslado = $params->hora_traslado;
        $traslado->hora_destino = $params->hora_destino;
        $traslado->id_tipo_egreso = $params->id_tipo_egreso;
        $traslado->dias_internacion = $params->dias_internacion;
        $traslado->gasto_total = $params->gasto_total;
        $traslado->gasto_extra = $params->gasto_extra;
        $traslado->observaciones = $params->observaciones;
        $traslado->diagnostico_final = $params->diagnostico_final;
        $traslado->id_derivacion_medico = $medico->id_derivacion_medico;
        $traslado->update();
        return $traslado;
    }

    public function findByUpdateEstado($id, $estado)
    {
        $traslado = DerivacionEntity::find($id);
        $traslado->id_tipo_estado = $estado;
        $traslado->update();
    }

    public function findByUpdateEstadoPresupuesto($id, $estado)
    {
        $traslado = DerivacionEntity::find($id);
        $traslado->estado_presupuesto = $estado;
        $traslado->update();
    }

    public function findById($id)
    {
        return DerivacionEntity::with([
            'medico',
            'estado',
            'afiliado',
            'medico.dlocalidad',
            'medico.hlocalidad',
            'obrasocial',
            'tipoPaciente',
            'tipoDerivacion',
            'tipoSector',
            'autorizacion',
            'autorizacion.usuario'
        ])
            ->find($id);
    }

    public function findByExistsInternacion($numInternacion)
    {
        return InternacionesEntity::where('num_internacion', $numInternacion)->exists();
    }

    public function findByAutorizarDerivacion($params)
    {
        return AutorizacionesDerivacionEntity::create([
            'id_derivacion' => $params->id_derivacion,
            'fecha_autorizacion' => $this->fechaActual,
            'cod_usuario' => $this->user->cod_usuario,
            'id_tipo_estado' => $params->id_tipo_estado,
            'observaciones' => $params->observaciones,
            'motivo_rechazo' => $params->motivo_rechazo
        ]);
    }
}
