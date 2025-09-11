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
        Schema::create('tb_desempleo', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('clave_desempleo',13)->nullable();
            $table->string('marca_fin_pago',1)->nullable();
            $table->string('parentesco',2)->nullable();
            $table->string('tipo_documento',2)->nullable();
            $table->string('nro_documento',8)->nullable();
            $table->string('provincia',3)->nullable();
            $table->string('cuil',11)->nullable();
            $table->string('fecha_nacimiento',10)->nullable();
            $table->string('nombres',50)->nullable();
            $table->string('fecha_vigencia',10)->nullable();
            $table->string('sexo',1)->nullable();
            $table->string('fecha_inicio_relacion',4)->nullable();
            $table->string('fecha_cese',4)->nullable();
            $table->string('rnos',6)->nullable();
            $table->string('fecha_proceso',8)->nullable();
            $table->string('cuil_titular',11)->nullable();
            $table->string('periodo_importacion',10)->nullable();
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
        Schema::dropIfExists('tb_desempleo');
    }
};
