<?php

namespace App\Http\Controllers\liquidaciones\dto;

class FacturaLiquidacionCabeceraDto
{
    public $num_liquidacion;
    public $cuit;
    public $prestador;
    public $prestador_fantasia;
    public $subtotal;
    public $total_iva;
    public $total_neto;
    public $monto_debitar;
    public $tipo_carga_detalle;
    public $delegacion;
    public $periodo;
    public $id_factura;
    public $id_prestador;
    public $estado;
    public $comprobante;
    public $imputacion_contable;

    public function __construct($num_liquidacion,  $cuit,  $prestador,  $prestador_fantasia, $subtotal,  $total_iva,  $total_neto,  $monto_debitar,  $tipo_carga_detalle,  $delegacion,  $periodo,  $id_factura,  $id_prestador,  $estado,  $comprobante, $imputacion_contable)
    {
        $this->num_liquidacion = $num_liquidacion;
        $this->cuit = $cuit;
        $this->prestador = $prestador;
        $this->prestador_fantasia = $prestador_fantasia;
        $this->subtotal = $subtotal;
        $this->total_iva = $total_iva;
        $this->total_neto = $total_neto;
        $this->monto_debitar = $monto_debitar;
        $this->tipo_carga_detalle = $tipo_carga_detalle;
        $this->delegacion = $delegacion;
        $this->periodo = $periodo;
        $this->id_factura = $id_factura;
        $this->id_prestador = $id_prestador;
        $this->estado = $estado;
        $this->comprobante = $comprobante;
        $this->imputacion_contable = $imputacion_contable;
    }
}
