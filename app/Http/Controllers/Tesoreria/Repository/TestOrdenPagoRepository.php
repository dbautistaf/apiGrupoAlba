<?php

namespace App\Http\Controllers\Tesoreria\Repository;

use App\Models\Tesoreria\TesEstadoOrdenPagoEntity;
use App\Models\Tesoreria\TesOrdenPagoDetalleEntity;
use App\Models\Tesoreria\TesOrdenPagoEntity;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TestOrdenPagoRepository
{

    private $user;
    private $fechaActual;

    public function __construct()
    {
        $this->user = Auth::user();
        $this->fechaActual = Carbon::now('America/Argentina/Buenos_Aires');
    }

    public function findByListTipoEstado()
    {
        return TesEstadoOrdenPagoEntity::get();
    }

    public function findListAlls($desde, $hasta)
    {
        return TesOrdenPagoEntity::with(['estado', 'proveedor', 'factura', 'prestador'])
            ->whereBetween(DB::raw('DATE(fecha_genera)'), [$desde, $hasta])
            ->orderByDesc('id_orden_pago')
            ->get();
    }

    public function findListBetweenAnEstado($estado, $desde, $hasta)
    {
        return TesOrdenPagoEntity::with(['estado', 'proveedor', 'factura', 'prestador'])
            ->where('id_estado_orden_pago', $estado)
            ->whereBetween(DB::raw('DATE(fecha_genera)'), [$desde, $hasta])
            ->orderByDesc('id_orden_pago')
            ->get();
    }

    public function findListBetweenAnTipo($tipo, $desde, $hasta)
    {
        return TesOrdenPagoEntity::with(['estado', 'proveedor', 'factura', 'prestador'])
            ->where('tipo_factura', $tipo)
            ->whereBetween(DB::raw('DATE(fecha_genera)'), [$desde, $hasta])
            ->orderByDesc('id_orden_pago')
            ->get();
    }

    public function findListBetweenAnTipoAndEstado($tipo, $estado, $desde, $hasta)
    {
        return TesOrdenPagoEntity::with(['estado', 'proveedor', 'factura', 'prestador'])
            ->where('tipo_factura', $tipo)
            ->where('id_estado_orden_pago', $estado)
            ->whereBetween(DB::raw('DATE(fecha_genera)'), [$desde, $hasta])
            ->orderByDesc('id_orden_pago')
            ->get();
    }

    public function findListBetweenAnTipoAndEstadoAndMontos($tipo, $estado, $desde, $hasta, $montDesde, $montHasta)
    {
        return TesOrdenPagoEntity::with(['estado', 'proveedor', 'factura', 'prestador'])
            ->where('tipo_factura', $tipo)
            ->where('id_estado_orden_pago', $estado)
            ->whereBetween('monto_orden_pago', [$montDesde, $montHasta])
            ->whereBetween(DB::raw('DATE(fecha_genera)'), [$desde, $hasta])
            ->orderByDesc('id_orden_pago')
            ->get();
    }

    public function findListBetweenAnTipoAndEstadoAndMontosAndBeneficiario($tipo, $estado, $desde, $hasta, $montDesde, $montHasta, $beneficiario)
    {
        return TesOrdenPagoEntity::with(['estado', 'proveedor', 'factura', 'prestador'])
            ->where('tipo_factura', $tipo)
            ->where('id_estado_orden_pago', $estado)
            ->whereBetween('monto_orden_pago', [$montDesde, $montHasta])
            ->whereBetween(DB::raw('DATE(fecha_genera)'), [$desde, $hasta])
            ->where(function ($query) use ($beneficiario) {
                $query->whereHas('proveedor', function ($q) use ($beneficiario) {
                    $q->where('razon_social', 'LIKE', "{$beneficiario}%");
                })->orWhereHas('prestador', function ($q) use ($beneficiario) {
                    $q->where('razon_social', 'LIKE', "{$beneficiario}%");
                });
            })
            ->orderByDesc('id_orden_pago')
            ->get();
    }

    public function findListBetweenAnEstadoAndBeneficiario($estado, $desde, $hasta, $beneficiario)
    {
        return TesOrdenPagoEntity::with(['estado', 'proveedor', 'factura', 'prestador'])
            ->where('id_estado_orden_pago', $estado)
            ->whereBetween(DB::raw('DATE(fecha_genera)'), [$desde, $hasta])
            ->where(function ($query) use ($beneficiario) {
                $query->whereHas('proveedor', function ($q) use ($beneficiario) {
                    $q->where('razon_social', 'LIKE', "{$beneficiario}%");
                })->orWhereHas('prestador', function ($q) use ($beneficiario) {
                    $q->where('razon_social', 'LIKE', "{$beneficiario}%");
                });
            })
            ->orderByDesc('id_orden_pago')
            ->get();
    }

    public function findListBetweenAndBeneficiario($desde, $hasta, $beneficiario)
    {
        return TesOrdenPagoEntity::with(['estado', 'proveedor', 'factura', 'prestador'])
            ->whereBetween(DB::raw('DATE(fecha_genera)'), [$desde, $hasta])
            ->where(function ($query) use ($beneficiario) {
                $query->whereHas('proveedor', function ($q) use ($beneficiario) {
                    $q->where('razon_social', 'LIKE', "{$beneficiario}%");
                })->orWhereHas('prestador', function ($q) use ($beneficiario) {
                    $q->where('razon_social', 'LIKE', "{$beneficiario}%");
                });
            })
            ->orderByDesc('id_orden_pago')
            ->get();
    }

    public function getFiltroDinamico($params)
    {
        $query = TesOrdenPagoEntity::with([
            'estado',
            'factura',
            'factura.razonSocial',
            'factura.comprobantes',
            'proveedor',
            'prestador',
            'pagoFecha.fechaprobablepagos'
        ]);

        if (!is_null($params->tipo)) {
            $query->where('tipo_factura', $params->tipo);
        }

        if (!is_null($params->estado)) {
            $query->where('id_estado_orden_pago', $params->estado);
        }

        if (!is_null($params->monto_desde) && !is_null($params->monto_hasta)) {
            $query->whereBetween('monto_orden_pago', [$params->monto_desde, $params->monto_hasta]);
        }

        if (!is_null($params->desde) && !is_null($params->hasta)) {
            $query->whereBetween(DB::raw('DATE(fecha_genera)'), [$params->desde, $params->hasta]);
        }

        if (!is_null($params->beneficiario)) {
            $query->where(function ($q) use ($params) {
                $q->whereHas('proveedor', function ($subQuery) use ($params) {
                    $subQuery->where('razon_social', 'LIKE', "{$params->beneficiario}%");
                })->orWhereHas('prestador', function ($subQuery) use ($params) {
                    $subQuery->where('razon_social', 'LIKE', "{$params->beneficiario}%");
                });
            });
        }

        if (!is_null($params->id_locatorio)) {
            $query->where(function ($q) use ($params) {
                $q->whereHas('factura', function ($subQuery) use ($params) {
                    $subQuery->where('id_locatorio', $params->id_locatorio);
                });
            });
        }

        if (!is_null($params->id_tipo_imputacion)) {
            $query->where(function ($q) use ($params) {
                $q->whereHas('factura', function ($subQuery) use ($params) {
                    $subQuery->where('id_tipo_imputacion_sintetizada', $params->id_tipo_imputacion);
                });
            });
        }

        if ($params->pago_urgente == '1') {
            $query->where('pago_emergencia', $params->pago_urgente);
        }

        if (!is_null($params->n_factura)) {
            $query->where(function ($q) use ($params) {
                $q->whereHas('factura', function ($subQuery) use ($params) {
                    $subQuery->where('numero', $params->n_factura);
                });
            });
        }

        return $query
            ->leftJoin('tb_prestador as p', 'p.cod_prestador', '=', 'tb_tes_orden_pago.id_prestador')
            ->leftJoin('tb_proveedor as prov', 'prov.cod_proveedor', '=', 'tb_tes_orden_pago.id_proveedor')
            ->orderByRaw('COALESCE(p.razon_social, prov.razon_social)')
            ->orderBy('monto_orden_pago')
            ->get();
    }

    public function findByOpaFactura($idFactura, $estado)
    {
        return TesOrdenPagoEntity::where('id_factura', $idFactura)
            ->where('id_estado_orden_pago', $estado)->first();
    }

    public function findByExistsOpaFacturaEstado($idFactura, $estado)
    {
        return TesOrdenPagoEntity::where('id_factura', $idFactura)
            ->where('id_estado_orden_pago', $estado)->exists();
    }

    public function findByCreate($param)
    {
        $opa = TesOrdenPagoEntity::create([
            'id_proveedor' => $param->id_proveedor,
            'id_prestador' => $param->id_prestador,
            'monto_orden_pago' => $param->monto_orden_pago,
            'id_moneda' => $param->id_moneda,
            'fecha_emision' => $param->fecha_emision,
            'fecha_vencimiento' => $param->fecha_vencimiento,
            'fecha_probable_pago' => $param->fecha_probable_pago,
            'id_estado_orden_pago' => $param->id_estado_orden_pago,
            'monto_anticipado' => $param->monto_anticipado,
            'observaciones' => $param->observaciones,
            'cod_usuario' => $this->user->cod_usuario,
            'fecha_genera' => $this->fechaActual,
            'id_factura' => $param->id_factura,
            'tipo_factura' => $param->tipo_factura
        ]);

        TesOrdenPagoDetalleEntity::create([
            'id_orden_pago' => $opa,
            'id_factura' => $param->id_factura,
            'monto_factura' => $param->monto_orden_pago,
            'tipo_factura' => $param->tipo_factura
        ]);
        return $opa;
    }

    public function findByUpdateOpaFactura($param)
    {
        $opa = TesOrdenPagoEntity::find($param->id_orden_pago);
        $opa->monto_orden_pago = $param->monto_orden_pago;
        $opa->update();
    }

    public function findByUpdate($param)
    {
        $tes = TesOrdenPagoEntity::find($param->id_orden_pago);
        // $tes->id_proveedor = $param->id_proveedor;
        // $tes->id_prestador = $param->id_prestador;
        // $tes->monto_orden_pago = $param->monto_orden_pago;
        $tes->id_moneda = $param->id_moneda;
        //$tes->fecha_emision = $param->fecha_emision;
        $tes->fecha_vencimiento = $param->fecha_vencimiento;
        $tes->fecha_probable_pago = $param->fecha_probable_pago;
        // $tes->id_estado_orden_pago = $param->id_estado_orden_pago;
        // $tes->monto_anticipado = $param->monto_anticipado;
        $tes->observaciones = $param->observaciones;
        // $tes->id_factura = $param->id_factura;
        $tes->pago_emergencia = $param->pago_emergencia;
        $tes->update();
        return $tes;
    }

    public function findByUpdateEstado($id_opa, $estado, $motivoRechazo = null)
    {
        $tes = TesOrdenPagoEntity::find($id_opa);
        $tes->id_estado_orden_pago = $estado;
        if ($estado == '3') {
            $tes->motivo_rechazo = $motivoRechazo;
            $tes->fecha_rechazo = $this->fechaActual;
        }
        $tes->update();
        return $tes;
    }

    public function findByConfirmarEstado($id_opa, $fechaPago, $estado)
    {
        $tes = TesOrdenPagoEntity::find($id_opa);
        $tes->id_estado_orden_pago = $estado;
        $tes->fecha_confirma_pago = $fechaPago;
        $tes->update();
        return $tes->load(['proveedor', 'factura', 'factura.tipoComprobante']);
    }

    public function findByConfirmarFechaProbablePago($id_opa, $fechaProbablePago, $cuotas)
    {
        $tes = TesOrdenPagoEntity::find($id_opa);
        $tes->fecha_probable_pago = $fechaProbablePago;
        $tes->cuotas = $cuotas;
        $tes->update();
        return $tes->load(['proveedor', 'factura', 'factura.tipoComprobante']);
    }

    public function findByConfirmarPagoEmergencia($id_opa, $emergencia)
    {
        $tes = TesOrdenPagoEntity::find($id_opa);
        $tes->pago_emergencia = $emergencia;
        $tes->update();
        return $tes->load(['proveedor', 'factura', 'factura.tipoComprobante']);
    }

    public function findByAnticipoPago($id_opa, $montoAnticipo)
    {
        $tes = TesOrdenPagoEntity::find($id_opa);
        $tes->monto_anticipado = $montoAnticipo;
        $tes->update();
        return $tes->load(['proveedor', 'factura', 'factura.tipoComprobante']);
    }

    public function findByExistsOpaEstado($id, $estado)
    {
        return TesOrdenPagoEntity::where('id_orden_pago', $id)
            ->where('id_estado_orden_pago', $estado)
            ->exists();
    }

    public function findById($idOpa)
    {
        return TesOrdenPagoEntity::find($idOpa);
    }

    public function findByIdFacturaEnProcesoOrPendiente($factura, $montoFactura)
    {
        // $idEstados = is_array([1, 4]);
        $opa = TesOrdenPagoEntity::where('id_factura', $factura)
            ->where('tipo_factura', 'PRESTADOR')
            ->whereIn('id_estado_orden_pago', [1, 4])
            ->first();
        if ($opa != null) {
            $opa->monto_orden_pago = $montoFactura;
            $opa->update();
        }
        return $opa ?? null;
    }

    public function findByIdFacturaMultiple($idFacturas)
    {
        $detalleOpa = TesOrdenPagoDetalleEntity::whereIn('id_factura', $idFacturas)->get();
        $idOrdenes = $detalleOpa->pluck('id_orden_pago')->toArray();

        $opa = TesOrdenPagoEntity::whereIn('id_orden_pago', $idOrdenes)->get();

        $first = $opa->first();

        $totalMonto = $opa->sum('monto_orden_pago');
        if ($first != null) {
            $newOpa = TesOrdenPagoEntity::create([
                'id_proveedor' => $first->id_proveedor ?? null,
                'id_prestador' => $first->id_prestador,
                'monto_orden_pago' => $totalMonto,
                'id_moneda' => $first->id_moneda,
                'fecha_emision' => $first->fecha_emision,
                'fecha_vencimiento' => $first->fecha_vencimiento,
                'fecha_probable_pago' => $first->fecha_probable_pago,
                'id_estado_orden_pago' => $first->id_estado_orden_pago,
                'monto_anticipado' => $first->monto_anticipado,
                'observaciones' => $first->observaciones,
                'cod_usuario' => $this->user->cod_usuario,
                'fecha_genera' => $this->fechaActual
            ]);

            foreach ($detalleOpa as $det) {
                TesOrdenPagoDetalleEntity::create([
                    'id_orden_pago' => $newOpa->id_orden_pago,
                    'id_factura' => $det->id_factura,
                    'monto_factura' => $det->monto_factura,
                    'tipo_factura' => $det->tipo_factura
                ]);
            }
            return $newOpa;
        }


        
    }
}
