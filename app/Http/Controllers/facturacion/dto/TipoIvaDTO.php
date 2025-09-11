<?php

namespace App\Http\Controllers\facturacion\dto;

class   TipoIvaDTO
{
    public $id_tipo_iva;
    public $descripcion;
    public $valor_iva;

    public function __construct($id_tipo_iva, $descripcion, $valor_iva)
    {
        $this->id_tipo_iva = $id_tipo_iva;
        $this->descripcion = $descripcion;
        $this->valor_iva = $valor_iva;
    }
}
