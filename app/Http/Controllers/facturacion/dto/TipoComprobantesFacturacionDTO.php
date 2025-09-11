<?php

namespace App\Http\Controllers\facturacion\dto;

class TipoComprobantesFacturacionDTO
{
    public $id_tipo_comprobante;
    public $descripcion;

    public function __construct($id_tipo_comprobante, $descripcion)
    {
        $this->id_tipo_comprobante = $id_tipo_comprobante;
        $this->descripcion = $descripcion;
    }
}
