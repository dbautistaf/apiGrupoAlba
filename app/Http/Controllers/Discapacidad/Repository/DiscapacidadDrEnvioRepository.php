<?php

namespace App\Http\Controllers\Discapacidad\Repository;

use App\Models\Discapacidad\DiscapacidadDrEnvioEntity;
use App\Models\Discapacidad\DiscapacidadTesoreriaEntity;
use Illuminate\Support\Facades\DB;

class DiscapacidadDrEnvioRepository
{

    public function findBySaveDrEnvior($params)
    {
        return DiscapacidadDrEnvioEntity::create([
            'clave_rendicion' => $params[0],
            'rnos' => $params[1],
            'tipo_archivo' => $params[2],
            'periodo_presentacion' => $params[3],
            'periodo_prestacion' => $params[4],
            'cuil' => $params[5],
            'cod_practica' => $params[6],
            'importe_subsidiado' => $this->findByExtrarCerosAdelante($params[7]),
            'importe_solicitado' => $this->findByExtrarCerosAdelante($params[8]),
            'cuit_prestador' => $params[9],
            'tipo_comprobante' => $params[10],
            'numero_comprobante' => $params[11],
            'punto_venta' => $params[12],
            'numero_envio_afip' => $params[13]
        ]);
    }

    public function findByCuitAndCuilAndNumComprobanteAndCodPracticaAndPeriodoPresentacion($cuit_prest, $cuil, $num_comprobante, $cod_practica, $periodo_prestacion)
    {
        return DB::table(
            "vw_rendicion_tesoreria",
        )
            ->where('cuit_prestador', $cuit_prest)
            ->where('cuil_beneficiario', $cuil)
            ->where('num_factura', $num_comprobante)
            ->where('id_practica', $cod_practica)
            ->where('periodo_prestacion', $periodo_prestacion)
            ->first();
    }

    public static function findByExtrarCerosAdelante($cadena)
    {
        $loak = str_replace(',', '.', $cadena);
        $numero = (float) $loak;
        return number_format($numero, 2, '.', '');
    }

    public function findByExisteIdRendicion($id)
    {
        return DiscapacidadDrEnvioEntity::where('clave_rendicion', $id)->exists();
    }

    public function findByAgregarClaveRendicion($claveRendicion, $id_discapacidad_tesoreria)
    {
        $disca =  DiscapacidadTesoreriaEntity::find($id_discapacidad_tesoreria);
        $disca->clave_rendicion = $claveRendicion;
        $disca->update();
    }
}
