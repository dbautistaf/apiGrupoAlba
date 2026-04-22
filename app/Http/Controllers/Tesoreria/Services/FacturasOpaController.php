<?php

namespace App\Http\Controllers\Tesoreria\Services;

use App\Http\Controllers\Tesoreria\Repository\FacturasOpaRepository;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;

class FacturasOpaController extends Controller
{
    protected $facturasOpaRepository;

    public function __construct(FacturasOpaRepository $facturasOpaRepository)
    {
        $this->facturasOpaRepository = $facturasOpaRepository;
    }

    /**
     * Obtiene facturas pendientes para agrupar por proveedor
     */
    public function getFacturasPendientesPorProveedor(Request $request)
    {
        try {
            $facturas = $this->facturasOpaRepository->findFacturasPendientesPorProveedor($request->id_proveedor);

            $facturas = $facturas->map(function ($factura) {
                return [
                    'id_factura' => $factura->id_factura,
                    'numero' => $factura->numero,
                    'fecha_comprobante' => $factura->fecha_comprobante,
                    'fecha_vencimiento' => $factura->fecha_vencimiento,
                    'total_neto' => $factura->total_neto,
                    'monto_pendiente' => $this->facturasOpaRepository->getMontoPendienteFactura($factura->id_factura),
                    'tipo_comprobante' => $factura->tipoComprobante->descripcion ?? '',
                    'tipo_letra' => $factura->tipo_letra,
                    'sucursal' => $factura->sucursal
                ];
            });

            return response()->json($facturas);
        } catch (\Throwable $th) {
            return response()->json(['message' => 'Error al obtener facturas: ' . $th->getMessage()], 500);
        }
    }

    /**
     * Obtiene facturas pendientes para agrupar por prestador
     */
    public function getFacturasPendientesPorPrestador(Request $request)
    {
        try {
            $facturas = $this->facturasOpaRepository->findFacturasPendientesPorPrestador($request->id_prestador);

            $facturas = $facturas->map(function ($factura) {
                return [
                    'id_factura' => $factura->id_factura,
                    'numero' => $factura->numero,
                    'fecha_comprobante' => $factura->fecha_comprobante,
                    'fecha_vencimiento' => $factura->fecha_vencimiento,
                    'total_neto' => $factura->total_neto,
                    'monto_pendiente' => $this->facturasOpaRepository->getMontoPendienteFactura($factura->id_factura),
                    'tipo_comprobante' => $factura->tipoComprobante->descripcion ?? '',
                    'tipo_letra' => $factura->tipo_letra,
                    'sucursal' => $factura->sucursal
                ];
            });

            return response()->json($facturas);
        } catch (\Throwable $th) {
            return response()->json(['message' => 'Error al obtener facturas: ' . $th->getMessage()], 500);
        }
    }

    /**
     * Relaciona una factura con una OPA específica
     */
    public function relacionarOpaFactura(Request $request)
    {
        try {
            $facturaOpa = $this->facturasOpaRepository->agregarFacturaOPA(
                $request->id_orden_pago,
                $request->id_factura,
                $request->monto_aplicado
            );

            return response()->json([
                'success' => true,
                'message' => 'Factura agregada a OPA exitosamente',
                'data' => $facturaOpa
            ]);
        } catch (\Throwable $th) {
            return response()->json(['message' => 'Error al relacionar factura con OPA: ' . $th->getMessage()], 500);
        }
    }

    /**
     * Obtiene todas las facturas de una OPA
     */
    public function getFacturasOpa($id)
    {
        try {
            $facturas = $this->facturasOpaRepository->getFacturasOPA($id);

            return response()->json([
                'success' => true,
                'facturas' => $facturas
            ]);
        } catch (\Throwable $th) {
            return response()->json(['message' => 'Error al obtener facturas de OPA: ' . $th->getMessage()], 500);
        }
    }

    /**
     * Quita una factura de una OPA
     */
    public function quitarFacturaOPA(Request $request)
    {
        try {
            $this->facturasOpaRepository->quitarFacturaOPA(
                $request->id_orden_pago,
                $request->id_factura
            );

            return response()->json([
                'success' => true,
                'message' => 'Factura removida de OPA exitosamente'
            ]);
        } catch (\Throwable $th) {
            return response()->json(['message' => 'Error al quitar factura de OPA: ' . $th->getMessage()], 500);
        }
    }

    /**
     * Actualiza el monto aplicado de una factura en una OPA
     */
    public function actualizarMontoFacturaOPA(Request $request)
    {
        try {
            $facturaOpa = $this->facturasOpaRepository->actualizarMontoFacturaOPA(
                $request->id_orden_pago,
                $request->id_factura,
                $request->monto_aplicado
            );

            return response()->json([
                'success' => true,
                'message' => 'Monto actualizado exitosamente',
                'data' => $facturaOpa
            ]);
        } catch (\Throwable $th) {
            return response()->json(['message' => 'Error al actualizar monto: ' . $th->getMessage()], 500);
        }
    }

    /**
     * Obtiene el saldo disponible de una factura
     */
    public function getSaldoFactura($idFactura)
    {
        try {
            $saldo = $this->facturasOpaRepository->getMontoPendienteFactura($idFactura);

            return response()->json([
                'success' => true,
                'saldo_pendiente' => $saldo
            ]);
        } catch (\Throwable $th) {
            return response()->json(['message' => 'Error al obtener saldo de factura: ' . $th->getMessage()], 500);
        }
    }

    /**
     * Lista facturas pendientes de un proveedor con saldos
     */
    public function listarFacturasPendientesProveedor($idProveedor)
    {
        try {
            $facturas = $this->facturasOpaRepository->listarFacturasPendientesProveedor($idProveedor);

            return response()->json([
                'success' => true,
                'facturas' => $facturas
            ]);
        } catch (\Throwable $th) {
            return response()->json(['message' => 'Error al listar facturas pendientes: ' . $th->getMessage()], 500);
        }
    }

    /**
     * Recalcula manualmente el monto total de una OPA
     */
    public function recalcularMontoOPA($idOrdenPago)
    {
        try {
            $this->facturasOpaRepository->recalcularMontoOPA($idOrdenPago);

            return response()->json([
                'success' => true,
                'message' => 'Monto de OPA recalculado exitosamente'
            ]);
        } catch (\Throwable $th) {
            return response()->json(['message' => 'Error al recalcular monto de OPA: ' . $th->getMessage()], 500);
        }
    }

    /**
     * Recalcula manualmente el estado de pago de una factura
     */
    public function recalcularEstadoPagoFactura($idFactura)
    {
        try {
            $this->facturasOpaRepository->recalcularEstadoPagoFactura($idFactura);

            return response()->json([
                'success' => true,
                'message' => 'Estado de pago de factura recalculado exitosamente'
            ]);
        } catch (\Throwable $th) {
            return response()->json(['message' => 'Error al recalcular estado de pago: ' . $th->getMessage()], 500);
        }
    }
}
