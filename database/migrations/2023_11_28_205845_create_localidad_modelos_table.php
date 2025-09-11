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
        Schema::create('tb_localidad', function (Blueprint $table) {
            $table->bigIncrements('id_localidad');
            $table->string('codigo_postal',10);
            $table->string('latitud', 100);
            $table->string('longitud', 100);
            $table->string('municipio', 100);
            $table->string('nombre', 45);
            $table->unsignedBigInteger('id_partido');
            $table->unsignedBigInteger('id_provincia');
            $table->foreign('id_provincia')->references('id_provincia')->on('tb_provincias');
            $table->foreign('id_partido')->references('id_partido')->on('tb_partidos');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tb_localidad');
    }
};
