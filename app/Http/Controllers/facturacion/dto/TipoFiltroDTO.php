<?php

namespace App\Http\Controllers\facturacion\dto;

class TipoFiltroDTO
{
    public $id_tipo;
    public $descripcion;

    public function __construct($id_tipo, $descripcion)
    {
        $this->id_tipo = $id_tipo;
        $this->descripcion = $descripcion;
    }
}
