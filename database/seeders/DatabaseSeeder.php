<?php

namespace Database\Seeders;

use App\Models\TipoMotivoRechazoAutotizacionEntity;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {

        $this->call([
            /* TipoImpuestoGananciasSeeder::class,
            TipoCondicionIvaSeeder::class,
            TipoPrestadorSeeder::class,
            TipoMatriculaSeeder::class,
            EspecialidadesSeeder::class
            TipoEstadoEstadoPrestacionSeeder::class
            TipoPlanesAfiliadoSeeder::class*/
            TipoMotivoRechazoAutotizacionSeeder::class

        ]);
    }
}
