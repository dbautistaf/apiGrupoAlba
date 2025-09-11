<?php

namespace Database\Seeders;

use App\Models\TipoPlanesAfiliadoEntity;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TipoPlanesAfiliadoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        TipoPlanesAfiliadoEntity::create(["cod_tipo_plan_afiliado" => "0","descripcion_corta" => "DESC", "descripcio_larga" => "Desconocido"]);
        TipoPlanesAfiliadoEntity::create(["cod_tipo_plan_afiliado" => "1","descripcion_corta" => "QUIMI", "descripcio_larga" => "Quimicos"]);
        TipoPlanesAfiliadoEntity::create(["cod_tipo_plan_afiliado" => "2","descripcion_corta" => "NOQUIMI", "descripcio_larga" => "No quimicos"]);
        TipoPlanesAfiliadoEntity::create(["cod_tipo_plan_afiliado" => "3","descripcion_corta" => "MONO", "descripcio_larga" => "Monotributistas"]);
        TipoPlanesAfiliadoEntity::create(["cod_tipo_plan_afiliado" => "1001","descripcion_corta" => "PMIBEBE", "descripcio_larga" => "PMI Bebe"]);
        TipoPlanesAfiliadoEntity::create(["cod_tipo_plan_afiliado" => "1002","descripcion_corta" => "PMIMAMA", "descripcio_larga" => "PMI Mama"]);
        TipoPlanesAfiliadoEntity::create(["cod_tipo_plan_afiliado" => "1003","descripcion_corta" => "PMIPUERPE", "descripcio_larga" => "PMI Puerperio"]);
        TipoPlanesAfiliadoEntity::create(["cod_tipo_plan_afiliado" => "1004","descripcion_corta" => "ONCOLOGICO", "descripcio_larga" => "Oncologicos"]);
        TipoPlanesAfiliadoEntity::create(["cod_tipo_plan_afiliado" => "1005","descripcion_corta" => "DISCAMO", "descripcio_larga" => "Discapacidad motora"]);
        TipoPlanesAfiliadoEntity::create(["cod_tipo_plan_afiliado" => "1006","descripcion_corta" => "DISCAME", "descripcio_larga" => "Discapacidad mental"]);
        TipoPlanesAfiliadoEntity::create(["cod_tipo_plan_afiliado" => "1007","descripcion_corta" => "DISCAMO", "descripcio_larga" => "Discapacidad motora"]);

    }
}
