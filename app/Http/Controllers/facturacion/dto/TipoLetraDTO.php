<?php

namespace App\Http\Controllers\facturacion\dto;

class   TipoLetraDTO
{
    public $tipo;
    public $descripcion;

    public function __construct($tipo, $descripcion )
    {
        $this->tipo = $tipo;
        $this->descripcion = $descripcion;
    }
}
