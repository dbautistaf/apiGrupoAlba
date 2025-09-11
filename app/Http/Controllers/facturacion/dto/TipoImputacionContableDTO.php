<?php

namespace App\Http\Controllers\facturacion\dto;

class TipoImputacionContableDTO
{
    public $id_tipo_imputacion;
    public $descripcion;

    public function __construct($id_tipo_imputacion, $descripcion)
    {
        $this->id_tipo_imputacion = $id_tipo_imputacion;
        $this->descripcion = $descripcion;
    }
}
