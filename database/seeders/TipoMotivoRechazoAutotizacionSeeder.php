<?php

namespace Database\Seeders;

use App\Models\TipoMotivoRechazoAutotizacionEntity;
use Illuminate\Database\Seeder;

class TipoMotivoRechazoAutotizacionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        TipoMotivoRechazoAutotizacionEntity::create(["descripcion_corta" => "Medicamento no incluido en FTN", "descripcion_larga" => "Medicamento no incluido en el Formulario Terapéutico Nacional."]);
        TipoMotivoRechazoAutotizacionEntity::create(["descripcion_corta" => "Interconsultas con especialistas", "descripcion_larga" => "Según las normas operativas vigentes las interconsultas con médicos especialistas deben ser solicitadas por el médico clínico responsable y con la correspondiente autorización de la Obra Social."]);
        TipoMotivoRechazoAutotizacionEntity::create(["descripcion_corta" => "Remitir historia clínica y plan terapéutico", "descripcion_larga" => "Se solicita remitir historia clínica y plan terapéutico indicado."]);
        TipoMotivoRechazoAutotizacionEntity::create(["descripcion_corta" => "A los fines de cumplimentar con las normas exigidas por la S.S. Salud solicitamos tenga a bien remitir historia clínica.", "descripcion_larga" => "Remitir historia clínica"]);
        TipoMotivoRechazoAutotizacionEntity::create(["descripcion_corta" => "No autorizado, falta diagnóstico.", "descripcion_larga" => "Falta diagnóstico"]);
        TipoMotivoRechazoAutotizacionEntity::create(["descripcion_corta" => "NO AUTORIZADO.", "descripcion_larga" => "NO AUTORIZADO"]);
        TipoMotivoRechazoAutotizacionEntity::create(["descripcion_corta" => "No corresponde cobertura", "descripcion_larga" => "No corresponde cobertura"]);

    }
}
