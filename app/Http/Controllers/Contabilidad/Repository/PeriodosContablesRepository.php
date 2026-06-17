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
        // Determinar id_tipo_periodo (1 = mensual, 2 = anual)
        $id_tipo_periodo = isset($params->mes) ? 1 : 2;

        // Generar periodo_contable
        $periodo_contable = $this->generatePeriodoContable($params->anio_periodo, $params->mes ?? null);

        // Generar periodo con los últimos 2 dígitos del año y 2 dígitos del mes
        $periodo = $this->generatePeriodo($params->anio_periodo, $params->mes ?? null);

        return PeriodosContablesEntity::create([
            'id_tipo_periodo' => $id_tipo_periodo,
            'id_razon' => $params->id_razon ?? null,
            'periodo' => $periodo,
            'anio_periodo' => $params->anio_periodo,
            'mes' => $params->mes ?? null,
            'periodo_contable' => $periodo_contable,
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

        // Determinar id_tipo_periodo (1 = mensual, 2 = anual)
        $id_tipo_periodo = isset($params->mes) ? 1 : 2;

        // Generar periodo_contable
        $periodo_contable = $this->generatePeriodoContable($params->anio_periodo, $params->mes ?? null);

        // Generar periodo con los últimos 2 dígitos del año y 2 dígitos del mes
        $periodoValue = $this->generatePeriodo($params->anio_periodo, $params->mes ?? null);

        $periodo->id_tipo_periodo = $id_tipo_periodo;
        $periodo->id_razon = $params->id_razon ?? null;
        $periodo->periodo = $periodoValue;
        $periodo->anio_periodo = $params->anio_periodo;
        $periodo->mes = $params->mes ?? null;
        $periodo->periodo_contable = $periodo_contable;
        $periodo->periodo_inicio = $params->periodo_inicio;
        $periodo->periodo_fin = $params->periodo_fin;
        $periodo->cod_usuario_modifica = $this->user->cod_usuario;
        $periodo->fecha_modifica = $this->fechaActual;
        $periodo->vigente = $params->vigente;
        $periodo->activo = $params->activo;
        return $periodo->update();
    }

    private function generatePeriodo($anio, $mes = null)
    {
        // Obtener los últimos 2 dígitos del año
        $anioCorto = substr($anio, -2);

        if ($mes) {
            // Formato para periodo mensual: últimos 2 dígitos del año + 2 dígitos del mes
            return $anioCorto . str_pad($mes, 2, '0', STR_PAD_LEFT);
        } else {
            // Para periodo anual, solo los últimos 2 dígitos del año + 00
            return $anioCorto . '00';
        }
    }

    private function generatePeriodoContable($anio, $mes = null)
    {
        if ($mes) {
            // Formato para periodo mensual: "Periodo 2024-02"
            return "Periodo {$anio}-" . str_pad($mes, 2, '0', STR_PAD_LEFT);
        } else {
            // Formato para periodo anual: "Periodo 2024"
            return "Periodo {$anio}";
        }
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

    public function findByExistsPeriodoAnual($anio, $idRazon = null)
    {
        $query = PeriodosContablesEntity::where('id_tipo_periodo', 2)
            ->where('anio_periodo', $anio)
            ->where('activo', '1');
        if ($idRazon) {
            $query->where('id_razon', $idRazon);
        }
        return $query->exists();
    }

    public function findByExistsPeriodoMensual($anio, $mes, $idRazon = null)
    {
        $query = PeriodosContablesEntity::where('id_tipo_periodo', 1)
            ->where('anio_periodo', $anio)
            ->where('mes', $mes)
            ->where('activo', '1');
        if ($idRazon) {
            $query->where('id_razon', $idRazon);
        }
        return $query->exists();
    }

    public function findByExistsPeriodoActivo($periodo, $idRazon = null)
    {
        $query = PeriodosContablesEntity::where('periodo', $periodo)->where('activo', '1');
        if ($idRazon) {
            $query->where('id_razon', $idRazon);
        }
        return $query->first();
    }

    public function findByPeriodoContableActivo($idRazon = null)
    {
        $query = PeriodosContablesEntity::where('activo', '1');
        if ($idRazon) {
            $query->where('id_razon', $idRazon);
        }
        return $query->first();
    }

    public function findByPeriodoContableActivoNow($idRazon = null)
    {
        $fecha = $this->fechaActual->toDateString();
        $query = PeriodosContablesEntity::where('activo', '1')
            ->where('id_tipo_periodo', 1)
            ->whereDate('periodo_inicio', '<=', $fecha)
            ->whereDate('periodo_fin', '>=', $fecha);
        if ($idRazon) {
            $query->where('id_razon', $idRazon);
        }
        return $query->first();
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

    public function findById($id)
    {
        return PeriodosContablesEntity::find($id);
    }

    /**
     * Abrir o cerrar (activo) el periodo anual y todos los mensuales del mismo año
     * $activo: true => abrir (1), false => cerrar (0)
     */
    public function setActivoByAnio($anio, bool $activo)
    {
        PeriodosContablesEntity::where('anio_periodo', $anio)
            ->update([
                'activo' => $activo ? '1' : '0',
                'cod_usuario_modifica' => $this->user->cod_usuario,
                'fecha_modifica' => $this->fechaActual
            ]);
    }
}
