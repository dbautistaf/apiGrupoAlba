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
        Schema::create('tb_baja_automatica_afip', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('cuil_tit',11)->nullable();
            $table->string('rnos',6)->nullable();
            $table->string('periodo',6)->nullable();
            $table->string('cuit',11)->nullable();
            $table->string('nombres',50)->nullable();
            $table->string('calle',20)->nullable();
            $table->string('numero',5)->nullable();
            $table->string('piso',4)->nullable();
            $table->string('depto',4)->nullable();
            $table->string('localidad',20)->nullable();
            $table->string('cp',10)->nullable();
            $table->string('provincia',15)->nullable();
            $table->string('categoria',2)->nullable();
            $table->string('periodo_import',10)->nullable();
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
        Schema::dropIfExists('tb_baja_automatica_afip');
    }
};
