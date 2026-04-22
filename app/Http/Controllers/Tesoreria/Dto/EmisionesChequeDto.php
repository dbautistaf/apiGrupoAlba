<?php
namespace App\Http\Controllers\Tesoreria\Dto;

class EmisionesChequeDto
{
    public $numero;
    public $fecha;
    public $beneficiario;
    public $monto;
    public $estado;
    public $pago_asociado;
    public $usuario;
    public function __construct($numero, $fecha, $beneficiario, $monto, $estado, $pago_asociado, $usuario)
    {
        $this->numero = $numero;
        $this->fecha = $fecha;
        $this->beneficiario = $beneficiario;
        $this->monto = $monto;
        $this->estado = $estado;
        $this->pago_asociado = $pago_asociado;
        $this->usuario = $usuario;
    }
}
