<?php

namespace App\Http\Controllers\Contabilidad\Repository;

use App\Models\Contabilidad\PeriodosContablesEntity;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class PeriodosContablesRepository
{

    private $user;
    private $fechaActual;

    public function __construct()
    {
        $this->user = Auth::user();
        $this->fechaActual = Carbon::now('America/Argentina/Buenos_Aires');
    }


    public function findByCreate($params)
    {
        return PeriodosContablesEntity::create([
            'anio_periodo' => $params->anio_periodo,
            'periodo_contable' => $params->periodo_contable,
            'periodo_inicio' => $params->periodo_inicio,
            'periodo_fin' => $params->periodo_fin,
            'cod_usuario_crea' => $this->user->cod_usuario,
            'fecha_registra' => $this->fechaActual,
            'vigente' => $params->vigente,
            'activo' => $params->activo
        ]);
    }

    public function findByUpdate($params, $id)
    {
        $periodo = PeriodosContablesEntity::find($id);
        $periodo->anio_periodo = $params->anio_periodo;
        $periodo->periodo_contable = $params->periodo_contable;
        $periodo->periodo_inicio = $params->periodo_inicio;
        $periodo->periodo_fin = $params->periodo_fin;
        $periodo->cod_usuario_modifica = $this->user->cod_usuario;
        $periodo->fecha_modifica = $this->fechaActual;
        $periodo->vigente = $params->vigente;
        $periodo->activo = $params->activo;
        return $periodo->update();
    }

    public function findByList($params)
    {
        $query = PeriodosContablesEntity::with([]);
        if (!is_null($params->estado)) {
            $query->where('activo', $params->estado);
        }
        $query->orderBy('periodo', 'desc');
        return $query->get();
    }
    public function findByListAnual($params)
    {
        $query = PeriodosContablesEntity::with([]);
        $query->where('id_tipo_periodo', 2);
        if (!is_null($params->estado)) {
            $query->where('activo', $params->estado);
        }
        $query->orderBy('periodo', 'desc');
        return $query->get();
    }

    public function findByExistsAnio($anio)
    {
        return PeriodosContablesEntity::where('anio_periodo', $anio)
            ->where('activo', '1')
            ->exists();
    }
    public function findByExistsPeriodoActivo($periodo)
    {
        return PeriodosContablesEntity::where('periodo', $periodo)
            ->where('activo', '1')
            ->first();
    }

    public function findByPeriodoContableActivo()
    {
        return PeriodosContablesEntity::where('activo', '1')
            ->first();
    }

    public function toggleActivo($id)
    {
        $periodo = PeriodosContablesEntity::find($id);
        $periodo->activo = !$periodo->activo;
        $periodo->cod_usuario_modifica = $this->user->cod_usuario;
        $periodo->fecha_modifica = $this->fechaActual;
        $periodo->save();
    }

    public function toggleVigente($id)
    {
        $periodo = PeriodosContablesEntity::find($id);
        $periodo->vigente = !$periodo->vigente;
        $periodo->cod_usuario_modifica = $this->user->cod_usuario;
        $periodo->fecha_modifica = $this->fechaActual;
        $periodo->save();
    }
}
