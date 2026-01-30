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

    public function findTopByFechaRecepcionBetweenAndEstado($desde, $hasta, $arrayEstados, $top)
    {
        $placeholder = implode(',', array_fill(0, count($arrayEstados), '?'));

        $data = DB::select(
            "SELECT * FROM vw_liquidacion_factura_unica WHERE fecha_registra_factura
            BETWEEN ? AND ? AND estado IN ($placeholder) ORDER BY prestador_fantasia, total_neto DESC LIMIT $top ",
            array_merge([$desde, $hasta], $arrayEstados)
        );

        return $this->collectAlls($data);
    }

    public function findTopByFechaRecepcionBetweenAndLocatario($desde, $hasta, $arrayEstados, $top, $locatario)
    {
        $placeholder = implode(',', array_fill(0, count($arrayEstados), '?'));

        $data = DB::select(
            "SELECT * FROM vw_liquidacion_factura_unica WHERE fecha_registra_factura
            BETWEEN ? AND ? AND id_locatorio= ? AND estado IN ($placeholder) ORDER BY prestador_fantasia, total_neto DESC LIMIT $top ",
            array_merge([$desde, $hasta, $locatario], $arrayEstados)
        );

        return $this->collectAlls($data);
    }

    public function findTopByFechaRecepcionBetweenAndEstadoAndCuitPrestadorLike($desde, $hasta, $arrayEstados, $cuitPrestador, $top)
    {
        $placeholder = implode(',', array_fill(0, count($arrayEstados), '?'));
        $sql = DB::table('vw_liquidacion_factura_unica')
            ->whereBetween('fecha_registra_factura', [$desde, $hasta])
            ->whereIn('estado', $arrayEstados);
        if (is_numeric($cuitPrestador)) {
            $sql->where('cuit', 'LIKE', $cuitPrestador . '%');
        } else {
            $sql->where('prestador', 'LIKE', '%' . $cuitPrestador . '%')
                ->orWhere('prestador_fantasia', 'LIKE', '%' . $cuitPrestador . '%');
        }
        $sql->orderByDesc('id_factura');
        $sql->limit($top);

        $data = $sql->get();


        return $this->collectAlls($data);
    }

    public function findTopByFechaRecepcionBetweenAndEstadoAndNumFacturaLike($desde, $hasta, $arrayEstados, $numComprobante, $top)
    {
        $placeholder = implode(',', array_fill(0, count($arrayEstados), '?'));

        $data = DB::select(
            "SELECT * FROM vw_liquidacion_factura_unica WHERE fecha_registra_factura
            BETWEEN ? AND ? AND comprobante LIKE ? AND estado IN ($placeholder) ORDER BY prestador_fantasia, total_neto DESC LIMIT $top ",
            array_merge([$desde, $hasta, '%' . $numComprobante . '%'], $arrayEstados)
        );

        return $this->collectAlls($data);
    }

    public function findTopByFechaRecepcionBetweenAndPeriodo($desde, $hasta, $arrayEstados, $periodo, $top)
    {
        $placeholder = implode(',', array_fill(0, count($arrayEstados), '?'));

        $data = DB::select(
            "SELECT * FROM vw_liquidacion_factura_unica WHERE fecha_registra_factura
            BETWEEN ? AND ? AND periodo=? AND estado IN ($placeholder) ORDER BY prestador_fantasia, total_neto DESC LIMIT $top ",
            array_merge([$desde, $hasta, $periodo], $arrayEstados)
        );

        return $this->collectAlls($data);
    }

    public function findTopByFechaRecepcionBetweenAndUsuarioLike($desde, $hasta, $arrayEstados, $usuario, $top)
    {
        $placeholder = implode(',', array_fill(0, count($arrayEstados), '?'));

        $data = DB::select(
            "SELECT * FROM vw_liquidacion_factura_unica WHERE fecha_registra_factura
            BETWEEN ? AND ? AND cod_usuario = ? AND estado IN ($placeholder) ORDER BY prestador_fantasia, total_neto DESC LIMIT $top ",
            array_merge([$desde, $hasta, $usuario], $arrayEstados)
        );
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
