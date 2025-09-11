<?php

namespace App\Http\Controllers\facturacion\dto;

class TipoFacturaDTO
{
    public $id_tipo_factura;
    public $descripcion;

    public function __construct($id_tipo_factura, $descripcion)
    {
        $this->id_tipo_factura = $id_tipo_factura;
        $this->descripcion = $descripcion;
    }
}
