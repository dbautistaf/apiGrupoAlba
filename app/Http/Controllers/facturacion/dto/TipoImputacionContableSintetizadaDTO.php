<?php

namespace App\Http\Controllers\facturacion\dto;

class TipoImputacionContableSintetizadaDTO
{
    public $id_tipo_imputacion_sintetizada;
    public $descripcion;

    public function __construct($id_tipo_imputacion_sintetizada, $descripcion)
    {
        $this->id_tipo_imputacion_sintetizada = $id_tipo_imputacion_sintetizada;
        $this->descripcion = $descripcion;
    }
}
