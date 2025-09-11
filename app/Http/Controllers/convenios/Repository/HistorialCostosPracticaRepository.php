<?php

namespace App\Http\Controllers\convenios\Repository;

use App\Models\convenios\ConvenioHistorialCostosPracticaEntity;
use App\Models\convenios\ConveniosPracticasEntity;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class HistorialCostosPracticaRepository
{

    public function findByPasarInactivoElMontoAnterior($montosAnteriores, $tipoAumento, $fechaVigencia)
    {
        $idHistorial = null;
        if ($this->findByExistsCostoPracticaLineal($montosAnteriores->id_identificador_practica, $montosAnteriores->cod_convenio, '1')) {
            $historialCosto = $this->findByUpdateCosto($montosAnteriores, '0', '0', $tipoAumento, $fechaVigencia);
            $idHistorial = $historialCosto->id_historial_pago;
        }
        return $idHistorial;
    }

    public function findByExistsCostoPractica($id_identificador_practica, $cod_convenio, $vigente)
    {
        return ConvenioHistorialCostosPracticaEntity::where('id_identificador_practica', $id_identificador_practica)
            ->where('cod_convenio', $cod_convenio)
            ->where('vigente', $vigente)
            ->exists();
    }

    public function findBySaveCosto($valores, $fecha_inicia, $fecha_corte_contrato, $vigente, $valorAumento, $tipoAumento)
    {
        $user = Auth::user();
        $fechaActual = Carbon::now('America/Argentina/Buenos_Aires');
        if ($this->findByExistsMontoPracticaActualizado($valores->id_identificador_practica, $valores->cod_convenio, '1')) {
            $now = Carbon::now('America/Argentina/Buenos_Aires')->format('Y-m-d');
            $practica = ConvenioHistorialCostosPracticaEntity::where('id_identificador_practica', $valores->id_identificador_practica)
                ->where('cod_convenio', $valores->cod_convenio)
                ->where('vigente', '1')
                ->whereDate('fecha_update', $now)
                ->first();
            $practica->monto_especialista = $valores->monto_especialista;
            $practica->monto_gastos = $valores->monto_gastos;
            $practica->monto_ayudante = $valores->monto_ayudante;
            $practica->cod_usuario_update = $user->cod_usuario;
            $practica->update();
        } else {
            ConvenioHistorialCostosPracticaEntity::create([
                'id_identificador_practica' => $valores->id_identificador_practica,
                'cod_convenio' => $valores->cod_convenio,
                'monto_especialista' => $valores->monto_especialista,
                'monto_gastos' => $valores->monto_gastos,
                'monto_ayudante' => $valores->monto_ayudante,
                'vigente' => $vigente,
                'tipo_carga' => 'UPDATE_COSTO',
                'fecha_inicio' => $fecha_inicia,
                'fecha_fin' => $fecha_corte_contrato,
                'fecha_update' => $fechaActual,
                'cod_usuario_crea' => $user->cod_usuario,
                'valor_aumento_lineal' => $valorAumento,
                'tipo_aumento' => $tipoAumento
            ]);
        }
    }

    public function findBySaveCostoManual($valores, $fecha_inicia, $fecha_corte_contrato, $vigente, $valorAumento, $tipoAumento, $id_historial_pago)
    {
        $user = Auth::user();
        $fechaActual = Carbon::now('America/Argentina/Buenos_Aires');
        if ($this->findByExistsMontoPracticaActualizado($valores->id_identificador_practica, $valores->cod_convenio, '1')) {
            $now = Carbon::now('America/Argentina/Buenos_Aires')->format('Y-m-d');
            $practica = ConvenioHistorialCostosPracticaEntity::where('id_identificador_practica', $valores->id_identificador_practica)
                ->where('id_historial_pago', $id_historial_pago)
                ->where('cod_convenio', $valores->cod_convenio)
                ->where('vigente', '1')
                ->whereDate('fecha_update', $now)
                ->first();
            $practica->monto_especialista = $valores->monto_especialista;
            $practica->monto_gastos = $valores->monto_gastos;
            $practica->monto_ayudante = $valores->monto_ayudante;
            $practica->cod_usuario_update = $user->cod_usuario;
            $practica->update();
        } else {
            ConvenioHistorialCostosPracticaEntity::create([
                'id_identificador_practica' => $valores->id_identificador_practica,
                'cod_convenio' => $valores->cod_convenio,
                'monto_especialista' => $valores->monto_especialista,
                'monto_gastos' => $valores->monto_gastos,
                'monto_ayudante' => $valores->monto_ayudante,
                'vigente' => $vigente,
                'tipo_carga' => 'UPDATE_COSTO',
                'fecha_inicio' => $fecha_inicia,
                'fecha_fin' => $fecha_corte_contrato,
                'fecha_update' => $fechaActual,
                'cod_usuario_crea' => $user->cod_usuario,
                'valor_aumento_lineal' => $valorAumento,
                'tipo_aumento' => $tipoAumento
            ]);
        }
    }

    public function findByUpdateCosto($valores, $vigente, $valorAumento, $tipoAumento, $fechaVigencia)
    {
        $user = Auth::user();
        $fechaActual = Carbon::now('America/Argentina/Buenos_Aires');
        $fechaCorte = Carbon::now('America/Argentina/Buenos_Aires')->subDay()->format('Y-m-d');

        if (!is_null($fechaVigencia)) {
            $fechaCorte = Carbon::parse($fechaVigencia)->subDay()->format('Y-m-d');
        }

        $practica = ConvenioHistorialCostosPracticaEntity::where('id_identificador_practica', $valores->id_identificador_practica)
            ->where('cod_convenio', $valores->cod_convenio)
            ->where('vigente', '1')
            ->first();

        $practica->monto_especialista = $valores->monto_especialista;
        $practica->monto_gastos = $valores->monto_gastos;
        $practica->monto_ayudante = $valores->monto_ayudante;
        $practica->vigente = $vigente;
        $practica->fecha_fin = $fechaCorte;
        $practica->fecha_update = $fechaActual;
        $practica->cod_usuario_update = $user->cod_usuario;
        $practica->valor_aumento_lineal = ($practica->valor_aumento_lineal + $valorAumento);
        $practica->update();
        return $practica;
    }

    public function findByExistsMontoPracticaActualizado($id_identificador_practica, $cod_convenio, $vigente)
    {
        $fechaActual = Carbon::now('America/Argentina/Buenos_Aires')->format('Y-m-d');

        return ConvenioHistorialCostosPracticaEntity::where('id_identificador_practica', $id_identificador_practica)
            ->where('cod_convenio', $cod_convenio)
            ->where('vigente', $vigente)
            ->whereDate("fecha_update", '=', $fechaActual)
            ->exists();
    }


    public function findBySaveCostosLineal($item, $fecha_inicio, $fecha_corte_contrato, $valorProcentaje, $monto, $tipo, $tipoAumento)
    {
        $user = Auth::user();
        if (!$this->findByExistsMontoPracticaActualizado($item->id_identificador_practica, $item->cod_convenio, '1')) {
            // $fechaInicio = $fecha_inicio;
            /* if ($this->findByExistsCostoPractica($item->id_identificador_practica, $item->cod_convenio, '0')) {
                $fechaInicio = Carbon::now('America/Argentina/Buenos_Aires');
            } */
            $fechaFinVigencia = Carbon::parse($fecha_inicio)->subDay()->format('Y-m-d');

            DB::update(
                "UPDATE tb_convenios_practicas SET vigente = '0',fecha_vigencia_hasta = ?  WHERE id_practica_convenio = ?",
                [$fechaFinVigencia, $item->id_practica_convenio]
            );
            $montoEs = 0;
            $montoGast = 0;
            $montoAyu = 0;
            if ($tipo == '1') {
                $montoEs = $monto;
            } else if ($tipo == '2') {
                $montoAyu = $monto;
            } else if ($tipo == '3') {
                $montoGast = $monto;
            }
            $montosNuevos = ConveniosPracticasEntity::create([
                'id_identificador_practica' => $item->id_identificador_practica,
                'cod_convenio' => $item->cod_convenio,
                'monto_especialista' => $montoEs,
                'monto_gastos' => $montoGast,
                'monto_ayudante' => $montoAyu,
                'vigente' => '1',
                'tipo_carga' => 'Gasto',
                'fecha_vigencia' => $fecha_inicio,
                'fecha_carga' => Carbon::now('America/Argentina/Buenos_Aires'),
                'cod_usuario_carga' => $user->cod_usuario,
                'fecha_vigencia_hasta' => $fecha_corte_contrato,
                'valor_aumento_lineal' => $valorProcentaje
            ]);

            $this->findBySaveCosto($montosNuevos, $fecha_inicio, $fecha_corte_contrato, '1', $valorProcentaje, $tipoAumento);
        }
    }

    public function findByGuardarMontoAnteriorLineal($montosAnteriores, $valorAumento, $tipoAumento, $fechaVigencia)
    {
        if ($this->findByExistsCostoPracticaLineal($montosAnteriores->id_identificador_practica, $montosAnteriores->cod_convenio, '1')) {
            $this->findByUpdateCosto($montosAnteriores, '0', $valorAumento, $tipoAumento, $fechaVigencia);
        }else if($fechaVigencia !== $montosAnteriores->fecha_inicio){
            $this->findByUpdateCosto($montosAnteriores, '0', $valorAumento, $tipoAumento, $fechaVigencia);
        }
    }

    public function findByExistsCostoPracticaLineal($id_identificador_practica, $cod_convenio, $vigente)
    {
        $fechaActual = Carbon::now('America/Argentina/Buenos_Aires')->format('Y-m-d');

        return ConvenioHistorialCostosPracticaEntity::where('id_identificador_practica', $id_identificador_practica)
            ->where('cod_convenio', $cod_convenio)
            ->where('vigente', $vigente)
            ->whereDate('fecha_update', '!=', $fechaActual)
            ->exists();
    }

    public function findByAumetoLinealExistenteDelDia($item, $monto, $valorAumento, $tipo, $tipoAumentoLineal, $fechaVigenciaManual)
    {
        if ($this->findByExistsMontoPracticaActualizado($item->id_identificador_practica, $item->cod_convenio, '1')) {
            $user = Auth::user();
            $practica = ConvenioHistorialCostosPracticaEntity::where('id_identificador_practica', $item->id_identificador_practica)
                ->where('cod_convenio', $item->cod_convenio)
                ->where('vigente', '1')
                ->first();
            if ($fechaVigenciaManual === $practica->fecha_inicio) {
                if ($tipo == '1') {
                    $practica->monto_especialista = $monto;
                } else if ($tipo == '2') {
                    $practica->monto_ayudante = $monto;
                } else if ($tipo == '3') {
                    $practica->monto_gastos = $monto;
                    DB::update("UPDATE tb_convenios_practicas SET valor_aumento_lineal = (valor_aumento_lineal + $valorAumento) ,monto_gastos = ?
                WHERE vigente = '1' AND id_practica_convenio = ?", [$monto, $item->id_practica_convenio]);
                }
                $practica->cod_usuario_update = $user->cod_usuario;
                $practica->valor_aumento_lineal = ($practica->valor_aumento_lineal + $valorAumento);
                if (is_null($practica->tipo_aumento)) {
                    $practica->tipo_aumento = $tipoAumentoLineal;
                }
                $practica->update();
            }
        }
    }

    public function findByGuardarHistorialCosto($valores, $fecha_inicia, $fecha_corte_contrato, $vigente, $tipoAumento, $idhistorialCosto)
    {
        $user = Auth::user();
        if (!$this->findByExistsCostoPracticaManual($valores->id_identificador_practica, $valores->cod_convenio, '1')) {
            $fechaInicio = $fecha_inicia;
            if ($this->findByExistsCostoPractica($valores->id_identificador_practica, $valores->cod_convenio, '0')) {
                $fechaInicio = Carbon::now('America/Argentina/Buenos_Aires');
            }
            $fechaFinVigencia = Carbon::now('America/Argentina/Buenos_Aires')->subDay()->format('Y-m-d');

            DB::update(
                "UPDATE tb_convenios_practicas SET vigente = '0',fecha_vigencia_hasta = ?  WHERE id_practica_convenio = ?",
                [$fechaFinVigencia, $valores->id_practica_convenio]
            );

            $montosNuevos = ConveniosPracticasEntity::create([
                'id_identificador_practica' => $valores->id_identificador_practica,
                'cod_convenio' => $valores->cod_convenio,
                'monto_especialista' => $valores->monto_especialista,
                'monto_gastos' => $valores->monto_gastos,
                'monto_ayudante' => $valores->monto_ayudante,
                'vigente' => '1',
                'tipo_carga' => 'Gasto',
                'fecha_vigencia' => $fechaInicio,
                'fecha_carga' => Carbon::now('America/Argentina/Buenos_Aires'),
                'cod_usuario_carga' => $user->cod_usuario,
                'fecha_vigencia_hasta' => $fecha_corte_contrato,
                'valor_aumento_lineal' => '0',
                'por_recaudacion' => $valores->por_recaudacion,
                'observaciones' => $valores->observaciones,
            ]);

            $this->findBySaveCostoManual($montosNuevos, $fechaInicio, $fecha_corte_contrato, '1', '0', $tipoAumento, $idhistorialCosto);
        } else {
            $practica = ConveniosPracticasEntity::find($valores->id_practica_convenio);
            $practica->monto_especialista = $valores->monto_especialista;
            $practica->monto_gastos = $valores->monto_gastos;
            $practica->monto_ayudante = $valores->monto_ayudante;
            $practica->por_recaudacion = $valores->por_recaudacion;
            $practica->observaciones = $valores->observaciones;
            $practica->update();

            $historial = ConvenioHistorialCostosPracticaEntity::where('id_identificador_practica', $valores->id_identificador_practica)
                ->where('cod_convenio', $valores->cod_convenio)
                ->where('vigente', '1')
                ->first();
            $historial->monto_especialista = $valores->monto_especialista;
            $historial->monto_gastos = $valores->monto_gastos;
            $historial->monto_ayudante = $valores->monto_ayudante;
            $historial->update();
        }
    }


    public function findByExistsCostoPracticaManual($id_identificador_practica, $cod_convenio, $vigente)
    {
        $fechaActual = Carbon::now('America/Argentina/Buenos_Aires')->format('Y-m-d');

        $val = ConvenioHistorialCostosPracticaEntity::where('id_identificador_practica', $id_identificador_practica)
            ->where('cod_convenio', $cod_convenio)
            ->where('vigente', $vigente)
            ->whereDate('fecha_update', $fechaActual)
            ->exists();
        return $val;
    }

    public function findByExistUltimoVigente($cod_convenio, $fechaVigencia)
    {

        $ultimaCarga = ConveniosPracticasEntity::where('cod_convenio', $cod_convenio)
            ->where('vigente', '1')
            ->orderByDesc('id_practica_convenio')
            ->limit(1)
            ->first();

        if (!is_null($ultimaCarga)) {
            $fechaEjecutar = Carbon::parse($fechaVigencia);
            $fechaFinContrato = Carbon::parse($ultimaCarga->fecha_vigencia);
            if ($fechaFinContrato->gte($fechaEjecutar)) {
                return true;
            }
        }

        return false;
    }
}
