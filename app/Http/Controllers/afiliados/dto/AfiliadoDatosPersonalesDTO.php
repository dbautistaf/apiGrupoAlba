<?php

namespace App\Http\Controllers\afiliados\dto;

class AfiliadoDatosPersonalesDTO
{
    public $id_afiliado;
    public $dni;
    public $cuil_titular;
    public $cuil_beneficiario;
    public $apellidos_nombres;
    public $fecha_nacimiento;
    public $estado;
    public $edad;
    public $numero_certificado;
    public $vencimiento_certificado;
    public $email;
    public $id_locatorio;
    public $planes;
    public $celular;
    public $direccion;
    public $parentesco;
    public $id_sexo;
    public function __construct($id_afiliado,  $dni,  $cuil_titular,  $cuil_beneficiario,  $apellidos_nombres,  $fecha_nacimiento,  $estado,  $edad,  $numero_certificado,  $vencimiento_certificado,  $email,  $id_locatorio,  $planes,  $celular,  $direccion,  $parentesco,$id_sexo)
    {
        $this->id_afiliado = $id_afiliado;
        $this->dni = $dni;
        $this->cuil_titular = $cuil_titular;
        $this->cuil_beneficiario = $cuil_beneficiario;
        $this->apellidos_nombres = $apellidos_nombres;
        $this->fecha_nacimiento = $fecha_nacimiento;
        $this->estado = $estado;
        $this->edad = $edad;
        $this->numero_certificado = $numero_certificado;
        $this->vencimiento_certificado = $vencimiento_certificado;
        $this->email = $email;
        $this->id_locatorio = $id_locatorio;
        $this->planes = $planes;
        $this->celular = $celular;
        $this->direccion = $direccion;
        $this->parentesco = $parentesco;
        $this->id_sexo = $id_sexo;
    }
}
