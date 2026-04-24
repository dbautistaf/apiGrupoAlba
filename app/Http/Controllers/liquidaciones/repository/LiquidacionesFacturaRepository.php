<?php

namespace App\Http\Controllers\liquidaciones\repository;

use App\Http\Controllers\liquidaciones\dto\FacturaLiquidacionCabeceraDto;
use App\Http\Controllers\liquidaciones\dto\LiquidacionesFacturaDto;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class LiquidacionesFacturaRepository
{

    public function findByIdFactura($idFactura)
    {
        $data = DB::select("SELECT
            num_liquidacion,cuit,prestador,prestador_fantasia,subtotal,total_iva,total_neto,total_debito,tipo_carga_detalle,
            delegacion,periodo,id_factura,id_prestador,estado,total_aprobado_liquidacion,total_facturado_liquidacion,comprobante, imputacion_contable
            FROM vw_liquidacion_factura_unica WHERE id_factura = ? ", [$idFactura]);

        return $this->collectCabecera($data);
    }

    public function findTopByFechaRecepcionBetweenAndEstadoAndNumFacturaLike($params)
    {

        $query = DB::table('vw_liquidacion_factura_unica');

        if (!empty($params->num_factura)) {
            $query->where('comprobante', 'like', '%' . $params->num_factura . '%');
        }

        if (!empty($params->desde) && !empty($params->hasta)) {
            $query->whereBetween('fecha_registra_factura', [$params->desde, $params->hasta]);
        }

        if (!empty($params->periodo)) {
            $query->where('periodo', $params->periodo);
        }

        if (!empty($params->estado)) {
            $query->where('estado', $params->estado);
        }

        if (!empty($params->id_locatario)) {
            $query->where('id_locatorio', $params->id_locatario);
        }

        if (!empty($params->cuit_prestador)) {
            $query->where(function ($q) use ($params) {
                $q->where('cuit', 'like', "%$params->cuit_prestador%")
                    ->orWhere('prestador', 'like', "%$params->cuit_prestador%")
                    ->orWhere('prestador_fantasia', 'like', "%$params->cuit_prestador%");
            });
        }

        // orden + límite
        $data = $query
            ->orderByDesc('fecha_registra_factura')
            ->orderBy('prestador_fantasia')
            ->orderByDesc('total_neto')
            ->get();

        return $this->collectAlls($data);
    }

    public function collectAlls($params)
    {
        return collect($params)
            ->map(function ($row) {
                return new LiquidacionesFacturaDto(
                    $row->cuit,
                    $row->prestador_fantasia,
                    $row->num_liquidacion,
                    $row->fecha_recepcion,
                    $row->fecha_vencimiento,
                    $row->fecha_liquidacion,
                    $row->comprobante,
                    ($row->refacturacion === '1' ? 'SI' : 'NO'),
                    $row->prestacion_externa,
                    $row->imputacion_contable,
                    $row->subtotal,
                    $row->total_iva,
                    $row->total_neto,
                    $row->total_debito,
                    $row->delegacion,
                    $row->periodo,
                    $row->tipo_carga_detalle,
                    $row->id_factura,
                    $row->id_tipo_factura,
                    $row->cod_sindicato,
                    $row->id_tipo_comprobante,
                    $row->id_tipo_imputacion_sintetizada,
                    $row->id_prestador,
                    $row->id_locatorio,
                    $row->estado,
                    $row->email_prestador,
                    $row->id_estado_orden_pago,
                    $row->id_orden_pago,
                    $row->estado_pago,
                    $row->locatorio,
                    $row->razon_social,
                    $row->tipo_prestador,
                    $row->tipo_proveedor,
                    $row->fecha_registra_factura,
                    $row->factura_unida,
                );
            });
    }

    public function collectCabecera($params)
    {
        return collect($params)
            ->map(function ($row) {
                return new FacturaLiquidacionCabeceraDto(
                    $row->num_liquidacion,
                    $row->cuit,
                    $row->prestador,
                    $row->prestador_fantasia,
                    $row->subtotal,
                    $row->total_iva,
                    $row->total_neto,
                    $row->total_debito,
                    $row->tipo_carga_detalle,
                    $row->delegacion,
                    $row->periodo,
                    $row->id_factura,
                    $row->id_prestador,
                    $row->estado,
                    $row->comprobante,
                    $row->imputacion_contable
                );
            });
    }
}
