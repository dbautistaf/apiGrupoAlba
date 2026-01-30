<?php

namespace App\Http\Controllers\liquidaciones\dto;

class LiquidacionesFacturaDto
{
    public $cuit;
    public $prestador;
    public $num_liquidacion;
    public $fecha_recepcion;
    public $fecha_vencimiento;
    public $fecha_liquidacion;
    public $comprobante;
    public $refacturacion;
    public $prestacion_externa;
    public $imputacion_contable;
    public $subtotal;
    public $total_iva;
    public $total_neto;
    public $total_debito;
    public $delegacion;
    public $periodo;
    public $tipo_carga_detalle;
    public $id_factura;
    public $id_tipo_factura;
    public $cod_sindicato;
    public $id_tipo_comprobante;
    public $id_tipo_imputacion_sintetizada;
    public $id_prestador;
    public $id_locatorio;
    public $estado;
    public $email_prestador;
    public $id_estado_orden_pago;
    public $id_orden_pago;
    public $id_estado_pago;
    public $locatario;
    public $razon_social;
    public $tipo_prestador;
    public $tipo_proveedor;
    public $fecha_registra;
    public $factura_unida;

    public function __construct($cuit,  $prestador,  $num_liquidacion,  $fecha_recepcion,  $fecha_vencimiento,  $fecha_liquidacion,  $comprobante,  $refacturacion,  $prestacion_externa,  $imputacion_contable,  $subtotal,  $total_iva,  $total_neto,  $total_debito,  $delegacion,  $periodo,  $tipo_carga_detalle,  $id_factura,  $id_tipo_factura,  $cod_sindicato,  $id_tipo_comprobante,  $id_tipo_imputacion_sintetizada,  $id_prestador,  $id_locatorio,  $estado,  $email_prestador,  $id_estado_orden_pago,  $id_orden_pago,  $id_estado_pago, $locatario, $razon_social, $tipo_prestador, $tipo_proveedor, $fecha_registra,$factura_unida)
    {
        $this->cuit = $cuit;
        $this->prestador = $prestador;
        $this->num_liquidacion = $num_liquidacion;
        $this->fecha_recepcion = $fecha_recepcion;
        $this->fecha_vencimiento = $fecha_vencimiento;
        $this->fecha_liquidacion = $fecha_liquidacion;
        $this->comprobante = $comprobante;
        $this->refacturacion = $refacturacion;
        $this->prestacion_externa = $prestacion_externa;
        $this->imputacion_contable = $imputacion_contable;
        $this->subtotal = $subtotal;
        $this->total_iva = $total_iva;
        $this->total_neto = $total_neto;
        $this->total_debito = $total_debito;
        $this->delegacion = $delegacion;
        $this->periodo = $periodo;
        $this->tipo_carga_detalle = $tipo_carga_detalle;
        $this->id_factura = $id_factura;
        $this->id_tipo_factura = $id_tipo_factura;
        $this->cod_sindicato = $cod_sindicato;
        $this->id_tipo_comprobante = $id_tipo_comprobante;
        $this->id_tipo_imputacion_sintetizada = $id_tipo_imputacion_sintetizada;
        $this->id_prestador = $id_prestador;
        $this->id_locatorio = $id_locatorio;
        $this->estado = $estado;
        $this->email_prestador = $email_prestador;
        $this->id_estado_orden_pago = $id_estado_orden_pago;
        $this->id_orden_pago = $id_orden_pago;
        $this->id_estado_pago = $id_estado_pago;
        $this->locatario = $locatario;
        $this->razon_social = $razon_social;
        $this->tipo_prestador = $tipo_prestador;
        $this->tipo_proveedor = $tipo_proveedor;
        $this->fecha_registra = $fecha_registra;
        $this->factura_unida = $factura_unida;
    }
}
