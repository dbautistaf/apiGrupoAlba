<?php

namespace Database\Seeders;

use App\Models\prestadores\TipoMatriculaMedicosEntity;
use Illuminate\Database\Seeder;

class TipoMatriculaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        TipoMatriculaMedicosEntity::create(["descripcion_matricula" => "Provincial"]);
        TipoMatriculaMedicosEntity::create(["descripcion_matricula" => "Nacional"]);
        TipoMatriculaMedicosEntity::create(["descripcion_matricula" => "Provincial - Nacional"]);
    }
}
