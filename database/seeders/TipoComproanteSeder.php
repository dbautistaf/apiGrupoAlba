<?php

namespace Database\Seeders;

use App\Models\TipoComprobanteModel;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TipoComproanteSeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        TipoComprobanteModel::create(['id_tipo_comprobante' => '01', 'tipo_comprobante' => 'FACTURAS A']);
        TipoComprobanteModel::create(['id_tipo_comprobante' => '02', 'tipo_comprobante' => 'RECIBOS A']);
        TipoComprobanteModel::create(['id_tipo_comprobante' => '03', 'tipo_comprobante' => 'FACTURAS B']);
        TipoComprobanteModel::create(['id_tipo_comprobante' => '04', 'tipo_comprobante' => 'RECIBOS B']);
        TipoComprobanteModel::create(['id_tipo_comprobante' => '05', 'tipo_comprobante' => 'FACTURAS C']);
        TipoComprobanteModel::create(['id_tipo_comprobante' => '06', 'tipo_comprobante' => 'RECIBOS C']);
        TipoComprobanteModel::create(['id_tipo_comprobante' => '07', 'tipo_comprobante' => 'FACTURAS M']);
        TipoComprobanteModel::create(['id_tipo_comprobante' => '08', 'tipo_comprobante' => 'RECIBOS M']);
    }
}
