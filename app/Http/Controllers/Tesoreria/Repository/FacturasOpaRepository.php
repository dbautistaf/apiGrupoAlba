<?php

namespace App\Http\Controllers\Tesoreria\Repository;

use App\Models\facturacion\FacturacionDatosEntity;
use App\Models\Tesoreria\TesFacturasOpaEntity;
use App\Models\Tesoreria\TesOrdenPagoEntity;
use App\Models\Tesoreria\TesEstadoPagoEntity;
use App\Models\Tesoreria\TesPagoEntity;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Log;

class FacturasOpaRepository
{
    private $user;
    private $fechaActual;

    public function __construct()
    {
        $this->user = Auth::user();
        $this->fechaActual = Carbon::now('America/Argentina/Buenos_Aires');
    }

    /**
     * Busca facturas pendientes para agrupar por proveedor
     */
    public function findFacturasPendientesPorProveedor($idProveedor)
    {
        return FacturacionDatosEntity::with(['tipoComprobante'])
            ->where('id_proveedor', $idProveedor)
            ->where('id_estado_pago', '!=', 3) // No pagadas completamente
            ->whereDoesntHave('tesFacturasOpa') // Sin OPA asignada
            ->get();
    }

    /**
     * Busca facturas pendientes para agrupar por prestador
     */
    public function findFacturasPendientesPorPrestador($idPrestador)
    {
        return FacturacionDatosEntity::with(['tipoComprobante'])
            ->where('id_prestador', $idPrestador)
            ->where('id_estado_pago', '!=', 3) // No pagadas completamente
            ->whereDoesntHave('tesFacturasOpa') // Sin OPA asignada
            ->get();
    }

    /**
     * Calcula el monto pendiente de una factura
     */
    public function getMontoPendienteFactura($idFactura)
    {
        $factura = FacturacionDatosEntity::find($idFactura);
        $montoPagado = TesFacturasOpaEntity::where('id_factura', $idFactura)->sum('monto_aplicado');

        return $factura ? ($factura->total_neto - $montoPagado) : 0;
    }

    /**
     * Obtiene el saldo disponible de una factura (descontando pagos confirmados)
     */
    public function getSaldoFactura($idFactura)
    {
        $factura = FacturacionDatosEntity::find($idFactura);

        if (!$factura) {
            throw new \Exception('Factura no encontrada');
        }

        // Sumar montos aplicados solo de OPAs CONFIRMADAS/PAGADAS
        $montoPagado = TesFacturasOpaEntity::join('tb_tes_orden_pago', 'tb_tes_opa_factura.id_orden_pago', '=', 'tb_tes_orden_pago.id_orden_pago')
            ->where('tb_tes_opa_factura.id_factura', $idFactura)
            ->whereNotNull('tb_tes_orden_pago.fecha_confirma_pago')
            ->sum('tb_tes_opa_factura.monto_aplicado');

        return $factura->total_neto - $montoPagado;
    }

    /**
     * Agrega una factura a una OPA con monto específico
     */
    public function agregarFacturaOPA($idOrdenPago, $idFactura, $montoAplicado)
    {
        return DB::transaction(function () use ($idOrdenPago, $idFactura, $montoAplicado) {
            // Validar saldo disponible
            $saldoPendiente = $this->getMontoPendienteFactura($idFactura);

            if ($montoAplicado > $saldoPendiente) {
                throw new \Exception('El monto aplicado excede el saldo pendiente de la factura');
            }

            $facturaOpa = TesFacturasOpaEntity::create([
                'id_orden_pago' => $idOrdenPago,
                'id_factura' => $idFactura,
                'monto_aplicado' => $montoAplicado,
                'fecha_imputacion' => $this->fechaActual,
                'cod_usuario' => $this->user->cod_usuario
            ]);

            $this->recalcularMontoOPA($idOrdenPago);
            $this->recalcularEstadoPagoFactura($idFactura);

            return $facturaOpa;
        });
    }

    /**
     * Quita una factura de una OPA
     */
    public function quitarFacturaOPA($idOrdenPago, $idFactura)
    {
        return DB::transaction(function () use ($idOrdenPago, $idFactura) {
            $facturaOpa = TesFacturasOpaEntity::where('id_orden_pago', $idOrdenPago)
                ->where('id_factura', $idFactura)
                ->first();

            if (!$facturaOpa) {
                throw new \Exception('Relación factura-OPA no encontrada');
            }

            $facturaOpa->delete();

            $this->recalcularMontoOPA($idOrdenPago);
            $this->recalcularEstadoPagoFactura($idFactura);

            return true;
        });
    }

    /**
     * Lista facturas pendientes para un proveedor específico
     */
    public function listarFacturasPendientesProveedor($idProveedor)
    {
        return FacturacionDatosEntity::where('id_proveedor', $idProveedor)
            ->whereIn('id_estado_pago', [0, 1, 2]) // IMPAGA, COMPROMETIDA, PARCIAL
            ->with(['proveedor', 'tipoComprobante'])
            ->get()
            ->map(function ($factura) {
                $factura->saldo_pendiente = $this->getMontoPendienteFactura($factura->id_factura);
                return $factura;
            });
    }

    /**
     * Recalcula el monto total de una OPA basado en sus facturas
     */
    public function recalcularMontoOPA($idOrdenPago)
    {
        $montoTotal = TesFacturasOpaEntity::where('id_orden_pago', $idOrdenPago)
            ->sum('monto_aplicado');

        TesOrdenPagoEntity::where('id_orden_pago', $idOrdenPago)
            ->update(['monto_orden_pago' => $montoTotal]);
    }

    /**
     * Recalcula el estado de pago de una factura basado en sus OPAs
     */
    public function recalcularEstadoPagoFactura($idFactura)
    {
        $factura = FacturacionDatosEntity::find($idFactura);

        if (!$factura) {
            throw new \Exception('Factura no encontrada');
        }

        // Pagado REAL = SUM(monto_aplicado) solo de OPAs con fecha_confirma_pago NOT NULL
        $pagadoReal = TesFacturasOpaEntity::join('tb_tes_orden_pago', 'tb_tes_opa_factura.id_orden_pago', '=', 'tb_tes_orden_pago.id_orden_pago')
            ->where('tb_tes_opa_factura.id_factura', $idFactura)
            ->whereNotNull('tb_tes_orden_pago.fecha_confirma_pago')
            ->sum('tb_tes_opa_factura.monto_aplicado');

        Log::info("Recalculando estado de factura ID: $idFactura, Pagado Real: $pagadoReal, Total Neto: {$factura->total_neto}");

        // Comprometida = existe en tb_tes_opa_factura
        $comprometida = TesFacturasOpaEntity::where('id_factura', $idFactura)->exists();

        $nuevoEstado = 0; // IMPAGA por defecto

        Log::info("Factura ID: $idFactura, Comprometida: " . ($comprometida ? 'Sí' : 'No'));

        if ($pagadoReal >= $factura->total_neto) {
            $nuevoEstado = 3; // PAGADA
        } elseif ($pagadoReal > 0) {
            $nuevoEstado = 2; // PARCIAL
        } elseif ($comprometida) {
            $nuevoEstado = 1; // COMPROMETIDA
        }

        $factura->update(['id_estado_pago' => $nuevoEstado]);
    }

    /**
     * Recalcula el estado de la factura basándose en los detalles de pago
     * SUM(pd.monto) JOIN p JOIN opaf WHERE opaf.id_factura = :idFactura AND p.id_estado_pago = CONFIRMADO
     */
    public function recalcularEstadoPagoFacturaFromDetalles($idFactura)
    {
        $factura = FacturacionDatosEntity::find($idFactura);
        if (!$factura) {
            throw new \Exception('Factura no encontrada');
        }

        $confirmado = TesEstadoPagoEntity::whereRaw('LOWER(descripcion_estado) = ?', ['confirmado'])->first();
        $confirmadoId = $confirmado ? $confirmado->id_estado_pago : 3;

        $totalPagado = (float) DB::table('tb_tes_pago_detalle as pd')
            ->join('tb_tes_pago as p', 'p.id_pago', '=', 'pd.id_pago')
            ->join('tb_tes_opa_factura as opaf', 'opaf.id_orden_pago', '=', 'p.id_orden_pago')
            ->where('opaf.id_factura', $idFactura)
            ->where('p.id_estado_pago', $confirmadoId)
            ->sum('pd.monto');

        $totalPagado = round($totalPagado, 2);
        $totalNeto = (float) $factura->total_neto;

        $nuevoEstado = 0; // IMPAGA
        if ($totalPagado >= $totalNeto && $totalNeto > 0) {
            $nuevoEstado = 3; // PAGADA
        } elseif ($totalPagado > 0 && $totalPagado < $totalNeto) {
            $nuevoEstado = 2; // PARCIAL
        } else {
            $nuevoEstado = 1; // COMPROMETIDA si existe relacion OPA
            // if no relation, keep IMPAGA
            if (!TesFacturasOpaEntity::where('id_factura', $idFactura)->exists()) {
                $nuevoEstado = 0;
            }
        }

        $factura->update(['id_estado_pago' => $nuevoEstado]);
        return $nuevoEstado;
    }

    /**
     * Obtiene todas las facturas de una OPA con su información detallada
     */
    public function getFacturasOPA($idOrdenPago)
    {
        return TesFacturasOpaEntity::with(['factura'])
            ->where('id_orden_pago', $idOrdenPago)
            ->get();
    }

    /**
     * Actualiza el monto aplicado de una factura en una OPA específica
     */
    public function actualizarMontoFacturaOPA($idOrdenPago, $idFactura, $nuevoMonto)
    {
        return DB::transaction(function () use ($idOrdenPago, $idFactura, $nuevoMonto) {
            $facturaOpa = TesFacturasOpaEntity::where('id_orden_pago', $idOrdenPago)
                ->where('id_factura', $idFactura)
                ->first();

            if (!$facturaOpa) {
                throw new \Exception('Relación factura-OPA no encontrada');
            }

            // Validar que el nuevo monto no exceda el saldo disponible
            $montoActual = $facturaOpa->monto_aplicado;
            $saldoDisponible = $this->getMontoPendienteFactura($idFactura) + $montoActual;

            if ($nuevoMonto > $saldoDisponible) {
                throw new \Exception('El nuevo monto excede el saldo disponible de la factura');
            }

            $facturaOpa->monto_aplicado = $nuevoMonto;

            $facturaOpa->save();

            $this->recalcularMontoOPA($idOrdenPago);
            $this->recalcularEstadoPagoFactura($idFactura);

            return $facturaOpa;
        });
    }
}
