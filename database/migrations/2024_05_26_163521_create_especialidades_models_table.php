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
        Schema::create('tb_especialidades', function (Blueprint $table) {
            $table->bigIncrements('id_especialidad');
            $table->string('especialidad', 50);
            $table->string('intervalo', 20);
            $table->boolean('activo')->default(1);
            $table->unsignedBigInteger('id_centro_medico');
            $table->foreign('id_centro_medico')->references('id_centro_medico')->on('tb_centros_medicos');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tb_especialidades');
    }
};
