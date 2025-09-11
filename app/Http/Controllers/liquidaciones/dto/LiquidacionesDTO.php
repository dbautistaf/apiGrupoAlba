<?php

namespace App\Http\Controllers\liquidaciones\dto;

class LiquidacionesDTO
{
    public $origen;
    public $cuil_afiliado;
    public $afiliado;
    public $edad_afiliado;
    public $tipo;
    public $ug;
    public $facturado;
    public $aprobado;
    public $debitado;
    public $usuario;
    public $estado;
    public $id_liquidacion;
    public $id_factura;

    public $total_coseguro;

    public function __construct($origen,  $cuil_afiliado,  $afiliado,  $edad_afiliado,  $tipo,  $ug,  $facturado,  $aprobado,  $debitado,  $usuario,  $estado,  $id_liquidacion,  $id_factura,  $total_coseguro)
    {
        $this->origen = $origen;
        $this->cuil_afiliado = $cuil_afiliado;
        $this->afiliado = $afiliado;
        $this->edad_afiliado = $edad_afiliado;
        $this->tipo = $tipo;
        $this->ug = $ug;
        $this->facturado = $facturado;
        $this->aprobado = $aprobado;
        $this->debitado = $debitado;
        $this->usuario = $usuario;
        $this->estado = $estado;
        $this->id_liquidacion = $id_liquidacion;
        $this->id_factura = $id_factura;
        $this->total_coseguro = $total_coseguro;
    }
}
