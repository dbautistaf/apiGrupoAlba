<?php

namespace App\Http\Controllers\Diabetes\Repository;

use App\Models\Afiliado\AfiliadoPadronEntity;
use App\Models\Diabetes\DetalleDiabetesEntity;
use App\Models\Diabetes\DiabetesEntity;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class DiabetesRepository
{
    private $user;
    private $fechaActual;
    public function __construct()
    {
        $this->user = Auth::user();
        $this->fechaActual = Carbon::now('America/Argentina/Buenos_Aires');
    }


    public function findByListarSolicitudes($filters)
    {
        $afiliado = AfiliadoPadronEntity::find($filters->id_padron);

        $jquery = DiabetesEntity::with(['tipoDiabetes', 'afiliado', 'detalle', 'detalle.medicamento'])
            ->where('dni_afiliado', $afiliado->dni);


        $jquery->whereBetween('fecha_alta', [$filters->desde, $filters->hasta]);
        $jquery->orderByDesc('id_diabetes');
        return $jquery->get();
    }

    public function findByCrear($params)
    {
        return DiabetesEntity::create([
            'dni_afiliado' => $params->dni_afiliado,
            'id_tipo_diabetes' => $params->id_tipo_diabetes,
            'fecha_alta' => $params->fecha_alta,
            'fecha_baja' => $params->fecha_baja,
            'id_padron' => $params->id_padron,
            'observaciones' => $params->observaciones,
            'cod_usuario_registra' => $this->user->cod_usuario,
            'fecha_registra' => $this->fechaActual
        ]);
    }

    public function findByUpdate($params)
    {
        $diabetes =  DiabetesEntity::find($params->id_diabetes);
        $diabetes->dni_afiliado = $params->dni_afiliado;
        $diabetes->id_tipo_diabetes = $params->id_tipo_diabetes;
        $diabetes->fecha_alta = $params->fecha_alta;
        $diabetes->fecha_baja = $params->fecha_baja;
        $diabetes->observaciones = $params->observaciones;
        $diabetes->cod_usuario_modifica = $this->user->cod_usuario;
        $diabetes->fecha_modifica = $this->fechaActual;
        $diabetes->id_padron = $params->id_padron;
        $diabetes->update();
        return $diabetes;
    }

    public function findByCrearDetalle($params, $id_diabetes)
    {
        return DetalleDiabetesEntity::create([
            'id_diabetes' => $id_diabetes,
            'id_medicamento' => $params['id_medicamento'],
            'fecha_inicio' => $params['fecha_inicio'],
            'fecha_fin' => $params['fecha_fin']
        ]);
    }

    public function findByUpdateItemDetalle($params, $id_diabetes)
    {
        $item = DetalleDiabetesEntity::find($params['id_diabetes_detalle']);
        $item->id_diabetes = $id_diabetes;
        $item->id_medicamento = $params['id_medicamento'];
        $item->fecha_inicio = $params['fecha_inicio'];
        $item->fecha_fin = $params['fecha_fin'];
    }

    public function findByEliminarItem($id)
    {
        $item = DetalleDiabetesEntity::find($id);
        return  $item->delete();
    }

    public function findByAnularSolicitud($id)
    {
        $item = DiabetesEntity::find($id);
        $item->vigente = 'ANULADO';
        $item->fecha_anula = $this->fechaActual;
        $item->cod_usuario_anula = $this->user->cod_usuario;
        $item->update();
        return  $item;
    }
}
