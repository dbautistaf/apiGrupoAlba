<?php

namespace Database\Seeders;

use App\Models\prestadores\TipoPrestadorEntity;
use Illuminate\Database\Seeder;

class TipoPrestadorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        TipoPrestadorEntity::create(["descripcion" => 'ESTUDIOS DE DIAGNOSTICO']);
        TipoPrestadorEntity::create(["descripcion" => 'MÉDICO ']);
        TipoPrestadorEntity::create(["descripcion" => 'ODONTOLÓGICO ']);
        TipoPrestadorEntity::create(["descripcion" => 'BIOQUÍMICO ']);
        TipoPrestadorEntity::create(["descripcion" => 'RADIOLÓGICO ']);
        TipoPrestadorEntity::create(["descripcion" => 'OTROS ']);
        TipoPrestadorEntity::create(["descripcion" => 'SANATORIAL ']);
        TipoPrestadorEntity::create(["descripcion" => 'INSUMOS ']);
        TipoPrestadorEntity::create(["descripcion" => 'PROTESIS / ORTESIS ']);
        TipoPrestadorEntity::create(["descripcion" => 'MEDICAMENTOS ']);
        TipoPrestadorEntity::create(["descripcion" => 'OFTALMOLOGíA ']);
        TipoPrestadorEntity::create(["descripcion" => 'MEDICAMENTOS ONCOLOGíCOS ']);
        TipoPrestadorEntity::create(["descripcion" => 'MEDICAMENTOS HIV ']);
        TipoPrestadorEntity::create(["descripcion" => 'PSICOLOGIA / PSIQUIATRIA ']);
        TipoPrestadorEntity::create(["descripcion" => 'OPTICAS ']);
        TipoPrestadorEntity::create(["descripcion" => 'ORTOPEDIA ']);
        TipoPrestadorEntity::create(["descripcion" => 'INSTITUTO ESCOLAR ']);
        TipoPrestadorEntity::create(["descripcion" => 'TRANSPORTE ESCOLAR (DISCAPACITADOS) ']);
        TipoPrestadorEntity::create(["descripcion" => 'MEDICAMENTOS CRÓNICOS ']);
        TipoPrestadorEntity::create(["descripcion" => 'MEDICAMENTOS POR RECETARIO ']);
        TipoPrestadorEntity::create(["descripcion" => 'KINESIOLOGíA ']);
        TipoPrestadorEntity::create(["descripcion" => 'FONOAUDIOLOGIA ']);
        TipoPrestadorEntity::create(["descripcion" => 'TERAPIA RADIANTE ']);
        TipoPrestadorEntity::create(["descripcion" => 'HOTELES ']);
        TipoPrestadorEntity::create(["descripcion" => 'DROGADEPENDENCIA ']);
        TipoPrestadorEntity::create(["descripcion" => 'Nivel I - Baja Complejidad ']);
        TipoPrestadorEntity::create(["descripcion" => 'Nivel II - Mediana Complejidad ']);
        TipoPrestadorEntity::create(["descripcion" => 'Nivel III - Alta Complejidad ']);
        TipoPrestadorEntity::create(["descripcion" => 'HEMODIALISIS ']);
        TipoPrestadorEntity::create(["descripcion" => 'REHABILITACION ']);
        TipoPrestadorEntity::create(["descripcion" => 'TERAPISTA FISICA ']);
        TipoPrestadorEntity::create(["descripcion" => 'ATENCION DOMICILIARIA']);
        TipoPrestadorEntity::create(["descripcion" => 'SERVICIOS DE AMBULANCIA ']);
        TipoPrestadorEntity::create(["descripcion" => 'ATENCIÓN MÉDICA PRIMARIA ']);
        TipoPrestadorEntity::create(["descripcion" => 'PROGRAMAS DE PREVENCIÓN ']);
    }
}
