<?php

namespace Database\Seeders;

use App\Models\prestadores\TipoCondicionIvaEntity;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TipoCondicionIvaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        TipoCondicionIvaEntity::create(["descripcion_iva" => "Responsable inscripto"]);
        TipoCondicionIvaEntity::create(["descripcion_iva" => "Monotributo"]);
        TipoCondicionIvaEntity::create(["descripcion_iva" => "Excento"]);
        TipoCondicionIvaEntity::create(["descripcion_iva" => "Contribuyente social"]);
        TipoCondicionIvaEntity::create(["descripcion_iva" => "Inscripto en ganacias"]);
        TipoCondicionIvaEntity::create(["descripcion_iva" => "Sin identificar"]);
    }
}
