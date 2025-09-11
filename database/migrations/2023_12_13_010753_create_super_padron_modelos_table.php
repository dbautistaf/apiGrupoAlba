<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tb_super_padron', function (Blueprint $table) {
            $table->bigIncrements('id_registro');
            $table->string('rnos',6);
            $table->string('cuit',11);
            $table->string('cuil_tit',11);
            $table->string('parentesco',2);
            $table->string('cuil_benef',11);
            $table->string('tipo_doc',2);
            $table->string('dni',8);
            $table->string('nombres',50);
            $table->string('sexo',1);
            $table->string('estado_civi',2);
            $table->string('fe_nac',10);
            $table->string('nacionalidad',6);
            $table->string('calle',30);
            $table->string('numero',10);
            $table->string('piso',10);
            $table->string('depto',10);
            $table->string('localidad',50);
            $table->string('cp',10);
            $table->string('id_prov',3);
            $table->string('sd2',10);
            $table->string('telefono',20);
            $table->string('sd3',10);
            $table->string('incapacidad',10);
            $table->string('sd5',10);
            $table->string('fe_alta',10);
            $table->string('fe_novedad',10);
            $table->string('periodo',10);
            $table->unsignedBigInteger('id_usuario');
            $table->foreign('id_usuario')->references('cod_usuario')->on('tb_usuarios');
            $table->string('fecha_importacion',10);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tb_super_padron');
    }
};
