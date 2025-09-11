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
        Schema::create('tb_efectores_sociales', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('cuit_titular',13)->nullable();
            $table->string('obra_social',6)->nullable();
            $table->string('nombres_efector',50)->nullable();
            $table->string('calle',20)->nullable();
            $table->string('numero',5)->nullable();
            $table->string('piso',5)->nullable();
            $table->string('departamento',6)->nullable();
            $table->string('localidad',20)->nullable();
            $table->string('codigo_postal',8)->nullable(); 
            $table->string('provincia',50)->nullable(); 
            $table->string('periodo_importacion',10)->nullable();
            $table->unsignedBigInteger('id_usuario')->nullable();
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
        Schema::dropIfExists('tb_efectores_sociales');
    }
};
