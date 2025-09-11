<?php

namespace App\Http\Controllers\afiliados\dto;

class PersonaAfipDTO
{
    public $apellido;
    public $calle;
    public $ciudad;
    public $cpostal;
    public $cuil;
    public $departamento;
    public $fechaNacimiento;
    public $fechaf;
    public $municipio;
    public $nombres;
    public $numeroDocumento;
    public $sexo;
    public $piso;
    public $provincia;
    public $edad;

    public function __construct($apellido, $calle, $ciudad, $cpostal, $cuil, $departamento, $fechaNacimiento, $fechaf, $municipio, $nombres, $numeroDocumento, $sexo, $piso, $provincia, $edad)
    {
        $this->apellido = $apellido;
        $this->calle = $calle;
        $this->ciudad = $ciudad;
        $this->cpostal = $cpostal;
        $this->cuil = $cuil;
        $this->departamento = $departamento;
        $this->fechaNacimiento = $fechaNacimiento;
        $this->fechaf = $fechaf;
        $this->municipio = $municipio;
        $this->nombres = $nombres;
        $this->numeroDocumento = $numeroDocumento;
        $this->sexo = $sexo;
        $this->piso = $piso;
        $this->provincia = $provincia;
        $this->edad = $edad;
    }
}
