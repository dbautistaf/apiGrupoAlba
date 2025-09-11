<?php

namespace App\Http\Controllers\Utils;

use Carbon\Carbon;

class GeneradorCodigosUtils
{

    private $fechaActual;
    private $user;

    public function __construct()
    {
        $this->fechaActual = Carbon::now('America/Argentina/Buenos_Aires');
    }

    public function getGenerarCodigoUnico($id)
    {
        $codigobarra = $this->fechaActual->format('ymdi');
        $codigobarra .= $id;
        $codigoVerifacion = $this->generarDigitoVerificador($codigobarra);
        $codigobarra .= $codigoVerifacion;
        return $codigobarra;
    }

    public static function generarDigitoVerificador($codigoBarra)
    {
        $multiplicador = 1;
        $sumaValorDigitos = 0;

        for ($i = 0; $i < strlen($codigoBarra); $i++) {
            $item = $codigoBarra[$i];
            $letra = (int) $item;

            // SECUENCIA: LOS PRIMEROS 5 DÍGITOS 1-3-5-7-9 Y LUEGO SE REPITE LA SUCESIÓN 3-5-7-9
            $sumaValorDigitos += $letra * $multiplicador;
            $multiplicador += 2;

            if ($multiplicador > 9) {
                $multiplicador = 3;
            }
        }

        $nmod2 = $sumaValorDigitos / 2;
        $mod10 = (int) $nmod2 % 10;

        return $mod10;
    }
}
