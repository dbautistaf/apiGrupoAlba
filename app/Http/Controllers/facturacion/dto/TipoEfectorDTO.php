<?php

namespace App\Http\Controllers\facturacion\dto;

class TipoEfectorDTO
{
    public $id_tipo_efector;
    public $descripcion;

    public function __construct($id_tipo_efector, $descripcion)
    {
        $this->id_tipo_efector = $id_tipo_efector;
        $this->descripcion = $descripcion;
    }
}
