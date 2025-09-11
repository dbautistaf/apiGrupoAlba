<?php

namespace Database\Seeders;


use App\Models\TipoEstadoPrestacionEntity;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TipoEstadoEstadoPrestacionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        TipoEstadoPrestacionEntity::create(["descripcion" => "AUTOTIZADA"]);
        TipoEstadoPrestacionEntity::create(["descripcion" => "PENDIENTE"]);
        TipoEstadoPrestacionEntity::create(["descripcion" => "NO AUTORIZADA"]);
        TipoEstadoPrestacionEntity::create(["descripcion" => "CERRADA"]);
        TipoEstadoPrestacionEntity::create(["descripcion" => "VENCIDA"]);
        TipoEstadoPrestacionEntity::create(["descripcion" => "PARCIALMENTE AUTORIZADA"]);
    }
}
