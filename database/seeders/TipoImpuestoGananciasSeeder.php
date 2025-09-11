<?php

namespace Database\Seeders;

use App\Models\prestadores\TipoImpuestosGananciasEntity;
use Illuminate\Database\Seeder;

class TipoImpuestoGananciasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        TipoImpuestosGananciasEntity::create(['descripcion_tipo' => 'Gravado']);
        TipoImpuestosGananciasEntity::create(['descripcion_tipo' => 'Excento']);
        TipoImpuestosGananciasEntity::create(['descripcion_tipo' => 'Sin Identificar']);
    }
}
