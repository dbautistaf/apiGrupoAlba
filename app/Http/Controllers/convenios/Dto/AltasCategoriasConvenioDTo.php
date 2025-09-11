<?php

namespace App\Http\Controllers\convenios\Dto;

class AltasCategoriasConvenioDTo
{

    public $id_alta_categoria;
    public $descripcion;
    public $grupo;
    public function __construct($id_alta_categoria,  $descripcion,  $grupo)
    {
        $this->id_alta_categoria = $id_alta_categoria;
        $this->descripcion = $descripcion;
        $this->grupo = $grupo;
    }
}
