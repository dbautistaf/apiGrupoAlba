<?php

namespace Database\Seeders;

use App\Models\PerfilModelo;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PerfilesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        PerfilModelo::create([
            'cod_perfil' => 1,
            'nombre_perfil' => 'Mesa Entrada',
            'estado' => true,
        ]);
    }
}
