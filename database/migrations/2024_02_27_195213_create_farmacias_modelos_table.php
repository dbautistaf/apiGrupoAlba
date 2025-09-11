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
        Schema::create('tb_farmacias', function (Blueprint $table) {
            $table->bigIncrements('id_farmacia');
            $table->date('fecha_alta');
            $table->date('fecha_baja');            
            $table->boolean('activo')->default(0);
            $table->unsignedBigInteger('id_usuario');
            $table->foreign('id_usuario')->references('cod_usuario')->on('tb_usuarios');
            $table->string('cuit', 11);
            $table->string('razon_social', 80);
            $table->string('domicilio', 50);
            $table->string('representante', 50);
            $table->unsignedBigInteger('id_localidad');
            $table->foreign('id_localidad')->references('id_localidad')->on('tb_localidad');
            $table->unsignedBigInteger('id_partido');
            $table->foreign('id_partido')->references('id_partido')->on('tb_partidos');
            $table->unsignedBigInteger('id_provincia');
            $table->foreign('id_provincia')->references('id_provincia')->on('tb_provincias');
            $table->text('observaciones')->nullable(); 
            $table->string('nombre_fantasia',100);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tb_farmacias');
    }
};
