<?php

namespace Database\Seeders;

use App\Models\TipoAreaModelo;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TipoAreasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        TipoAreaModelo::create(['tipo_area' => 'AFILIACIONES']);
        TipoAreaModelo::create(['tipo_area' => ' AREA CONTABLE']);
        TipoAreaModelo::create(['tipo_area' =>'AREA MEDICA']);
        TipoAreaModelo::create(['tipo_area' =>'AUDITORIA MEDICA']);
        TipoAreaModelo::create(['tipo_area' =>'AUTORIZACIONES ESPECIALES']);
        TipoAreaModelo::create(['tipo_area' =>'CONVENIOS']);
        TipoAreaModelo::create(['tipo_area' =>'COORDINACION GENERAL']);
        TipoAreaModelo::create(['tipo_area' =>'DIRECCION MEDICA']);
        TipoAreaModelo::create(['tipo_area' =>'DISCAPACIDAD']);
        TipoAreaModelo::create(['tipo_area' =>'FACTURACION FARMACIA']);
        TipoAreaModelo::create(['tipo_area' =>'GERENCIA']);
        TipoAreaModelo::create(['tipo_area' =>'INSUMOS']);
        TipoAreaModelo::create(['tipo_area' =>'INTEGRACION']);
        TipoAreaModelo::create(['tipo_area' =>'LIQUIDACIONES']);
        TipoAreaModelo::create(['tipo_area' =>'MEDICAMENTOS DE ALTO COSTO']);
        TipoAreaModelo::create(['tipo_area' =>'ODONTOLOGIA']);
        TipoAreaModelo::create(['tipo_area' =>'PRESIDENCIA']);
        TipoAreaModelo::create(['tipo_area' =>'PROTESIS']);
        TipoAreaModelo::create(['tipo_area' =>'RECURSOS HUMANOS']);
        TipoAreaModelo::create(['tipo_area' =>'SURGE']);
        TipoAreaModelo::create(['tipo_area' =>'TESORERIA']);
        TipoAreaModelo::create(['tipo_area' =>'TURISMO']);
        TipoAreaModelo::create(['tipo_area' =>'VERIFICACIONES Y COBRANZAS']);
    }
}
