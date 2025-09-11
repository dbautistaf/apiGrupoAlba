<?php

namespace Database\Seeders;

use App\Models\TipoDocumentacionModelo;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TipoDocumentacionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        TipoDocumentacionModelo::create(['tipo_documentacion' => 'CARTA DOCUMENTO']);
        TipoDocumentacionModelo::create(['tipo_documentacion' => 'CEDULA DE NOTIFICACION']);
        TipoDocumentacionModelo::create(['tipo_documentacion' => 'CHEQUE']);
        TipoDocumentacionModelo::create(['tipo_documentacion' => 'FACTURACION']);
        TipoDocumentacionModelo::create(['tipo_documentacion' => 'MEDICAMENTOS']);
        TipoDocumentacionModelo::create(['tipo_documentacion' => 'NOTAS DE CREDITO']);
        TipoDocumentacionModelo::create(['tipo_documentacion' => 'NOTIFICACIONES VARIAS']);
        TipoDocumentacionModelo::create(['tipo_documentacion' => 'OFICIO JUDICIAL']);
        TipoDocumentacionModelo::create(['tipo_documentacion' => 'RECIBO']);
        TipoDocumentacionModelo::create(['tipo_documentacion' => 'TELEGRAMA']);
    }
}
