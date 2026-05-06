<?php

namespace App\Http\Controllers\Tesoreria\Repository;

use App\Models\Tesoreria\TesFechaProbablePagoEntity;
use App\Models\Tesoreria\TestChequesEntity;
use App\Models\Tesoreria\TesPagoEntity;
use App\Models\Tesoreria\TesPagosParciales;
use App\Models\Tesoreria\TestDetalleComprobantesPagoEntity;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Tesoreria\TesPagoDetalleEntity;
use App\Models\Tesoreria\TesOrdenPagoEntity;
use App\Models\Tesoreria\TesEstadoOrdenPagoEntity;
use App\Models\Tesoreria\TesEstadoPagoEntity;
use App\Models\Tesoreria\TesFacturasOpaEntity;
use App\Http\Controllers\Tesoreria\Repository\FacturasOpaRepository;
use App\Models\Tesoreria\PagoRetencionesEntity;

class TesPagosRepository
{
    private $user;
    private $fechaActual;

    public function __construct()
    {
        $this->user = Auth::user();
        $this->fechaActual = Carbon::now('America/Argentina/Buenos_Aires');
    }

    public function findByCrearPago($params)
    {
        $pago = TesPagoEntity::create([
            'id_orden_pago'         => $params['id_orden_pago'],
            'id_cuenta_bancaria'    => $params['id_cuenta_bancaria'],
            'fecha_registra'        => $this->fechaActual,
            'fecha_confirma_pago'   => $params['fecha_confirma_pago'],
            'anticipo'              => $params['anticipo'],
            'comprobante'           => $params['comprobante'],
            'id_forma_pago'         => $params['id_forma_pago'],
            'monto_pago'            => $params['monto_pago'],
            'observaciones'         => $params['observaciones'],
            'id_estado_orden_pago'  => $params['id_estado_orden_pago'],
            'id_usuario'            => $this->user->cod_usuario,
            'monto_opa'             => $params['monto_opa'],
            'recursor'              => $params['recursor'],
            'fecha_probable_pago'   => null,
            'tipo_factura'          => $params['tipo_factura'],
            'pago_emergencia'       => $params['pago_emergencia'],
        ]);

        foreach ($params['cuotas'] as $cuotas) {
            TesFechaProbablePagoEntity::create([
                'fecha_registra' => $this->fechaActual,
                'fecha_probable_pago' => $cuotas['fecha_probable_pago'],
                'orden_cuotas' => $cuotas['orden_cuotas'],
                'id_pago' => $pago->id_pago,
            ]);
        }
        return $pago;
    }

    public function findByAsignarCodigoVerificacion($id, $codigo)
    {
        $pago = TesPagoEntity::find($id);
        $pago->num_pago = $codigo;
        $pago->update();
        return $pago;
    }

    public function findByListPagosBetween($desde, $hasta)
    {
        return TesPagoEntity::with(['estado', 'cuenta', 'formaPago', 'opa.proveedor', 'opa.factura.razonSocial', 'comprobantes'])
            ->whereBetween(DB::raw('DATE(fecha_probable_pago)'), [$desde, $hasta])
            ->orderBy('id_estado_orden_pago', 'asc')
            ->orderBy('fecha_probable_pago')
            ->get();
    }

    public function findByListPagosFiltroPrincipal($params)
    {
        $jquery = "";
        $tipoRelacion = $params->tipo === 'PROVEEDOR' ? 'proveedor' : 'prestador';
        $tipoFactura = $params->tipo === 'PROVEEDOR' ? 'PROVEEDOR' : 'PRESTADOR';

        $jquery = TesPagoEntity::with([
            'estado',
            'cuenta',
            'formaPago',
            "opa.$tipoRelacion",
            "opa.$tipoRelacion.datosBancarios",
            //'opa.factura.razonSocial',
            'opa.opadetalle.detallefc',
            'comprobantes',
            'pagosParciales',
            'pagosParciales.formaPago',
            'fechaprobablepagos',
            'detalleopa.detallefc',
            'detalleopa.detallefc.razonSocial'
        ]);

        $jquery->where('tipo_factura', $tipoFactura);

        if (!is_null($params->beneficiario)) {
            $jquery->whereHas("opa.$tipoRelacion", function ($query) use ($params) {
                $query->where(function ($q) use ($params) {
                    $q->where('razon_social', 'like', '%' . $params->beneficiario . '%')
                        ->orWhere('nombre_fantasia', 'like', '%' . $params->beneficiario . '%');
                });
            });
        }

        if (!is_null($params->desde) && !is_null($params->hasta)) {
            $jquery->whereHas('fechaprobablepagos', function ($query) use ($params) {
                $query->whereBetween(DB::raw('DATE(fecha_probable_pago)'), [$params->desde, $params->hasta]);
            });
        }

        /*  if (!is_null($params->monto_desde) && !is_null($params->monto_hasta)) {
            $jquery->whereBetween('monto_pago', [$params->monto_desde, $params->monto_hasta]);
        } */

        if (!is_null($params->numero_opa)) {
            $jquery->whereHas('opa', function ($query) use ($params) {
                $query->where('num_orden_pago', $params->numero);
            });
        }

        if (!is_null($params->estado)) {
            $jquery->where('id_estado_orden_pago', $params->estado);
        }

        if ($params->pago_urgente == '1') {
            $jquery->where('pago_emergencia', $params->pago_urgente);
        }

        if (!is_null($params->id_locatario)) {
            $jquery->whereHas('detalleopa.detallefc', function ($query) use ($params) {
                $query->where('id_locatorio', $params->id_locatario);
            });
        }

        if (!is_null($params->id_tipo_imputacion)) {
            $jquery->whereHas('detalleopa.detallefc', function ($query) use ($params) {
                $query->where('id_tipo_imputacion_sintetizada', $params->id_tipo_imputacion);
            });
        }

        if (!is_null($params->numero)) {
            $jquery->whereHas('detalleopa.detallefc', function ($query) use ($params) {
                $query->where('numero', $params->numero);
            });
        }

        if (!is_null($params->id_tipo) && $params->id_tipo !== '') {
            $jquery->whereHas('detalleopa.detallefc', function ($query) use ($params) {
                $query->where('id_tipo_factura', '=', (int) $params->id_tipo);
            });
        }

        // $jquery->orderBy('id_estado_orden_pago', 'asc');
        $jquery->orderBy('fecha_probable_pago');
        return $jquery->get();
    }

    public function findByConfirmarPago($params)
    {

        $pagosparciales = 0;
        $estado = null;
        $pago = TesPagoEntity::find($params->id_pago);

        foreach ($params->lista_pagos as $pagos) {
            $pagosparciales = $pagosparciales + $pagos->monto_pago;
            if (empty($pagos->id_pago_parcial)) {
                TesPagosParciales::create([
                    'fecha_registra' => $this->fechaActual,
                    'fecha_confirma_pago' => $pagos->fecha_confirma_pago,
                    'id_forma_pago' => $pagos->id_forma_pago,
                    'monto_pago' => $pagos->monto_pago,
                    'monto_opa' => $pagos->monto_opa,
                    'num_cheque' => $pagos->num_cheque,
                    'id_usuario' => $this->user->cod_usuario,
                    'id_pago' => $pago->id_pago,
                    'monto_restante' => $pagos->monto_restante,
                ]);
            }else{
                $query=TesPagosParciales::find($pagos->id_pago_parcial);
                $query->fecha_registra=$this->fechaActual;
                $query->fecha_confirma_pago=$pagos->fecha_confirma_pago;
                $query->id_forma_pago=$pagos->id_forma_pago;
                $query->monto_pago=$pagos->monto_pago;
                $query->monto_opa=$pagos->monto_opa;
                $query->num_cheque=$pagos->num_cheque;
                $query->id_usuario=$this->user->cod_usuario;
                $query->id_pago=$pagos->id_pago;
                $query->monto_restante=$pagos->monto_restante;
                $query->update();
            }
        }
        if ($pagosparciales < $pago->monto_opa) {
            $estado = 6;
        } elseif ($pagosparciales == $pago->monto_opa) {
            $estado = 5;
        }

        $pago->id_cuenta_bancaria = $params->id_cuenta_bancaria;
        $pago->fecha_confirma_pago = $this->fechaActual;
        $pago->id_forma_pago = 0;
        $pago->monto_pago = $params->anticipo == '1' ? $params->monto_anticipado : $params->monto_pago;
        $pago->id_estado_orden_pago = 5;
        $pago->anticipo = $params->anticipo;
        $pago->monto_anticipado = $params->monto_anticipado;
        $pago->num_cheque = $params->num_cheque;
        $pago->fecha_probable_pago = $params->fecha_probable_pago;
        $pago->observaciones = $params->observaciones;

        $pago->id_forma_cobro = is_numeric($params->id_forma_cobro) ? $params->id_forma_cobro : null;
        $pago->monto_cobro = $params->monto_cobro;
        $pago->fecha_confirma_cobro = $params->fecha_confirma_cobro;
        $pago->cuenta_bancaria = $params->cuenta_bancaria;
        $pago->imputacion_contable = $params->imputacion_contable;
        $pago->banco = $params->banco;

        $pago->update();

        return $pago;
    }

    public function findByAnularPago($id_pago, $observacion)
    {
        $pago = TesPagoEntity::find($id_pago);
        $pago->motivo_rechazo = $observacion;
        $pago->id_estado_orden_pago = 3;
        $pago->fecha_rechazo = $this->fechaActual;
        $pago->update();
        return $pago;
    }

    public function findByUpdatePagoPorOpa($params, $idOpa)
    {
        $pago = TesPagoEntity::where('id_orden_pago', $idOpa)->first();
        $pago->fecha_probable_pago = $params->fecha_probable_pago;
        $pago->pago_emergencia = $params->pago_emergencia;
        $pago->update();
        return $pago;
    }

    public function findById($id)
    {
        return TesPagoEntity::find($id);
    }

    public function findBySumarDetallePagosAnticipados($idOpa)
    {
        return (float) TesPagoEntity::where('id_orden_pago', $idOpa)->sum('monto_anticipado');
    }

    public function findByListDetallePagosAnticipadosConfirmados($idOpa)
    {
        return TesPagoEntity::with(['formaPago', 'comprobantes'])
            ->where('id_orden_pago', $idOpa)
            ->where('id_estado_orden_pago', '5')
            ->get();
    }

    public function findByCargarComprobantePago($archivo, $idPago)
    {
        return TestDetalleComprobantesPagoEntity::create([
            'id_pago' => $idPago,
            'nombre_archivo' => $archivo,
            'fecha_registra' => $this->fechaActual,
            'cod_usuario_registra' => $this->user->cod_usuario,
            'estado' => '1'
        ]);
    }

    public function findByDeleteComprobantePago($idPago)
    {
        $archivo = TestDetalleComprobantesPagoEntity::findOrFail($idPago);
        $archivo->cod_usuario_elimina = $this->user->cod_usuario;
        $archivo->fecha_elimina = $this->fechaActual;
        $archivo->estado = '0';
        $archivo->update();
        return $archivo;
    }

    public function findByUpdateOpaPagoFacturaLiquidaciones($idOpa, $monto)
    {
        if (TesPagoEntity::where('id_orden_pago', $idOpa)->where('id_estado_orden_pago', '1')->exists()) {

            if (TesPagoEntity::where('id_orden_pago', $idOpa)->where('id_estado_orden_pago', '1')->where('anticipo', '0')->exists()) {
                DB::update("UPDATE tb_tes_pago SET monto_opa = ?, monto_pago = ? WHERE tipo_factura = 'PRESTADOR' AND id_orden_pago = ? ", [$monto, $monto, $idOpa]);
            } else {
                DB::update("UPDATE tb_tes_pago SET monto_opa = ? WHERE tipo_factura = 'PRESTADOR' AND id_orden_pago = ? ", [$monto, $idOpa]);
            }

            return true;
        }
        return false;
    }

    public function findByCrearCheque($cheque)
    {
        return TestChequesEntity::create([
            'id_cuenta_bancaria' => $cheque->id_cuenta_bancaria,
            'tipo_cheque' => 'TERCERO',
            'numero_cheque' => $cheque->num_cheque,
            'monto' => $cheque->monto_pago,
            'fecha_emision' => $cheque->fecha_confirma_pago,
            'fecha_vencimiento' => $cheque->fecha_confirma_pago,
            'tipo' => 'EMISION',
            'estado' => 'ACTIVO',
            'descripcion' => null,
            'archivo_adjunto' => null,
            'cod_usuario_registra' => $this->user->cod_usuario,
            'fecha_registra' => $this->fechaActual,
            'beneficiario' => $cheque->beneficiario,
            'numero_cheque_anterior' => null,
            'is_echeck' => 0,
            'id_chequera' => $cheque->id_chequera
        ]);
    }

    public function findByListTipoEstado()
    {
        return TesEstadoPagoEntity::get();
    }

    /**
     * Determina si es el primer pago del mes para un prestador específico
     * 
     * @param int $idPrestador ID del prestador
     * @param string $fecha Fecha del pago a verificar (Y-m-d o Carbon)
     * @return bool true si es el primer pago del mes, false si no
     */
    public function esPrimerPagoDelMes($idPrestador, $fecha)
    {
        try {
            $fechaPago = $fecha instanceof Carbon ? $fecha : Carbon::parse($fecha);

            $primerDiaMes = $fechaPago->copy()->startOfMonth()->toDateString();
            $ultimoDiaMes = $fechaPago->copy()->endOfMonth()->toDateString();

            // Obtener IDs de pagos del prestador en ese mes que tengan detalles confirmados
            $pagosDelPrestador = TesPagoEntity::whereHas('opa.prestador', function ($q) use ($idPrestador) {
                $q->where('cod_prestador', $idPrestador);
            })
                ->where('tipo_factura', 'PRESTADOR')
                ->pluck('id_pago');

            // Verificar si alguno de esos pagos ya tiene detalles con fecha_acreditacion en el mes
            $detallesEnElMes = TesPagoDetalleEntity::whereIn('id_pago', $pagosDelPrestador)
                ->whereBetween(DB::raw('DATE(fecha_acreditacion)'), [$primerDiaMes, $ultimoDiaMes])
                ->exists();

            return !$detallesEnElMes;

        } catch (\Throwable $e) {
            return false;
        }
    }

    /**
     * Determina si es el primer pago del mes basado en el ID del pago
     * 
     * @param int $idPago ID del pago a verificar
     * @return bool true si es el primer pago del mes, false si no
     */
    public function esPrimerPagoDelMesPorIdPago($idPago)
    {
        try {
            $pago = TesPagoEntity::with('opa.prestador')->find($idPago);

            if (!$pago || !$pago->opa || !$pago->opa->prestador) {
                return false;
            }

            return $this->esPrimerPagoDelMes($pago->opa->prestador->cod_prestador, Carbon::now());

        } catch (\Throwable $e) {
            return false;
        }
    }

        /**
     * Recalcula y actualiza el monto total pagado para un pago (sum de sus detalles)
     */
    public function recalcPagoTotal($idPago)
    {
        $total = (float) TesPagoDetalleEntity::where('id_pago', $idPago)
            ->selectRaw('SUM(monto) as total')
            ->value('total');

        $totalRetenido = (float) PagoRetencionesEntity::where('id_pago', $idPago)
            ->sum('monto');

        $pago = TesPagoEntity::find($idPago);
        if ($pago) {
            $pago->monto_total_pagado = round($total, 2);
            $pago->monto_total_retenido = round($totalRetenido, 2);
            $pago->update();
        }
        return $total;
    }

    /**
     * Recalcula el estado del pago a partir de sus detalles (monto pagado)
     * - Si suma == 0 => GENERADO (1)
     * - Si 0 < suma < monto_opa => EN PROCESO (2)
     * - Si suma >= monto_opa => CONFIRMADO (3)
     * Actualiza `monto_total_pagado` y `id_estado_pago`, y recalcula el estado de la OPA.
     */
    public function recalcPagoEstadoFromDetalles($idPago)
    {
        try {
            $sumDetalles = (float) TesPagoDetalleEntity::where('id_pago', $idPago)->sum('monto');
            $sumRetenciones = (float) PagoRetencionesEntity::where('id_pago', $idPago)->sum('monto');
            $pago = TesPagoEntity::find($idPago);
            if (!$pago) {
                return null;
            }

            $montoOpa = (float) ($pago->monto_opa ?? 0);

            $generadoId = $this->getPagoEstadoIdByName('GENERADO') ?? 1;
            $enProcesoId = $this->getPagoEstadoIdByName('EN PROCESO') ?? 2;
            $confirmadoId = $this->getPagoEstadoIdByName('CONFIRMADO') ?? 3;

            if (abs($sumDetalles) < 0.0001) {
                $nuevoEstado = $generadoId;
            } elseif ($sumDetalles + $sumRetenciones + 0.0001 >= $montoOpa) {
                $nuevoEstado = $confirmadoId;
            } else {
                $nuevoEstado = $enProcesoId;
            }

            $pago->monto_total_pagado = round($sumDetalles, 2);
            $pago->monto_total_retenido = round($sumRetenciones, 2);
            $pago->id_estado_pago = $nuevoEstado;
            $pago->update();

            // Recalcular estado de la OPA asociado al pago
            if (isset($pago->id_orden_pago)) {
                $this->recalcOpaTotalsAndState($pago->id_orden_pago);
            }

            return $pago;
        } catch (\Throwable $e) {
            return null;
        }
    }

    /**
     * Recalcula el total pagado de la OPA (sum de monto_total_pagado en tb_tes_pago)
     * y actualiza el estado de la OPA según la regla proporcionada.
     */
    public function recalcOpaTotalsAndState($idOpa)
    {
        // Nuevo comportamiento:
        // - total_pagado_confirmado = SUM(monto_total_pagado) WHERE id_estado_pago = CONFIRMADO
        // - si existen pagos EN PROCESO -> OPA = EN PROCESO
        // - si total = 0 -> PENDIENTE
        // - si 0 < total < monto_orden -> PARCIALMENTE PAGADA
        // - si total >= monto_orden -> PAGADA

        $opa = TesOrdenPagoEntity::find($idOpa);
        if (!$opa)
            return null;

        $montoOrden = (float) ($opa->monto_orden_pago ?? 0);

        $confirmadoId = $this->getPagoEstadoIdByName('CONFIRMADO') ?? 3;
        $enProcesoPagoId = $this->getPagoEstadoIdByName('EN PROCESO') ?? 2;

        $totalConfirmado = (float) TesPagoEntity::where('id_orden_pago', $idOpa)
            ->where('id_estado_pago', $confirmadoId)
            ->sum(DB::raw('monto_total_pagado + monto_total_retenido'));

        $existenEnProceso = TesPagoEntity::where('id_orden_pago', $idOpa)
            ->where('id_estado_pago', $enProcesoPagoId)
            ->exists();

        $pendienteId = $this->getEstadoIdByName('Pendiente') ?? 1;
        $enProcesoOpaId = $this->getEstadoIdByName('En Proceso') ?? 2;
        $parcialmentePagadaId = $this->getEstadoIdByName('Parcialmente Pagada') ?? $this->getEstadoIdByName('Parcial') ?? 3;
        $pagadaId = $this->getEstadoIdByName('Pagada') ?? 4;

        if ($existenEnProceso) {
            $nuevoEstado = $enProcesoOpaId;
        } elseif (abs($totalConfirmado) < 0.0001) {
            $nuevoEstado = $pendienteId;
        } elseif ($totalConfirmado > 0 && $totalConfirmado < $montoOrden) {
            $nuevoEstado = $parcialmentePagadaId;
        } else {
            $nuevoEstado = $pagadaId;
        }

        if (!is_null($nuevoEstado)) {
            $opa->id_estado_orden_pago = $nuevoEstado;
            $opa->update();
        }

        return [
            'total_pagado_confirmado' => $totalConfirmado,
            'monto_orden' => $montoOrden,
            'estado' => $nuevoEstado,
            'existen_pagos_en_proceso' => $existenEnProceso
        ];
    }

    private function getPagoEstadoIdByName($name)
    {
        if (empty($name))
            return null;
        $estado = TesEstadoPagoEntity::whereRaw('LOWER(descripcion_estado) = ?', [strtolower($name)])->first();
        return $estado ? $estado->id_estado_pago : null;
    }

    private function getEstadoIdByName($name)
    {
        if (empty($name))
            return null;
        $estado = TesEstadoOrdenPagoEntity::whereRaw('LOWER(descripcion_estado) = ?', [strtolower($name)])->first();
        return $estado ? $estado->id_estado_orden_pago : null;
    }

}
