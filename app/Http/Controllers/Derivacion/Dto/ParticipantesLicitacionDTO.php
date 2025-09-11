<?php

namespace App\Http\Controllers\Derivacion\Dto;

class ParticipantesLicitacionDTO
{
    public $id_solicitud;
    public $cod_prestador;
    public $cuit;
    public $nombre_fantasia;
    public $razon_social;
    public $fecha_solicita_presupuesto;
    public $email;
    public $celular;
    public $archivo;
    public $ganador;
    public $montoCotiza;
    public $observaciones;

    public function __construct($id_solicitud, $cod_prestador, $cuit, $nombre_fantasia, $razon_social, $fecha_solicita_presupuesto, $email, $celular, $archivo, $ganador, $montoCotiza, $observaciones)
    {
        $this->id_solicitud = $id_solicitud;
        $this->cod_prestador = $cod_prestador;
        $this->cuit = $cuit;
        $this->nombre_fantasia = $nombre_fantasia;
        $this->razon_social = $razon_social;
        $this->fecha_solicita_presupuesto = $fecha_solicita_presupuesto;
        $this->email = $email;
        $this->celular = $celular;
        $this->archivo = $archivo;
        $this->ganador = $ganador;
        $this->montoCotiza = $montoCotiza;
        $this->observaciones = $observaciones;
    }
}
