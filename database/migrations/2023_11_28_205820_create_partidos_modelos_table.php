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
        Schema::create('tb_partidos', function (Blueprint $table) {
            $table->bigIncrements('id_partido');
            $table->string('codigo_indec', 45);
            $table->string('nombre', 50);
            $table->unsignedBigInteger('id_provincia');
            $table->foreign('id_provincia')->references('id_provincia')->on('tb_provincias');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tb_partidos');
    }
};
