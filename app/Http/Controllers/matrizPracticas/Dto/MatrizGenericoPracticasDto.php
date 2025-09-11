<?php

namespace App\Http\Controllers\matrizPracticas\Dto;

class MatrizGenericoPracticasDto
{
    public $nomenclador;
    public $seccion;
    public $padre;
    public $codigo_practica;
    public $nombre_practica;
    public $id_nomenclador;
    public $id_seccion;
    public $id_padre;
    public $id_identificador_practica;
    public $estado;
    public function __construct($nomenclador,  $seccion,  $padre,  $codigo_practica,  $nombre_practica,  $id_nomenclador,  $id_seccion,  $id_padre,  $id_identificador_practica,  $estado)
    {
        $this->nomenclador = $nomenclador;
        $this->seccion = $seccion;
        $this->padre = $padre;
        $this->codigo_practica = $codigo_practica;
        $this->nombre_practica = $nombre_practica;
        $this->id_nomenclador = $id_nomenclador;
        $this->id_seccion = $id_seccion;
        $this->id_padre = $id_padre;
        $this->id_identificador_practica = $id_identificador_practica;
        $this->estado = $estado;
    }
}
