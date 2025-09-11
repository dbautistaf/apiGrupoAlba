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
        Schema::create('tb_altas_monotributo', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('tipo', 2);
            $table->string('formulario', 10);
            $table->string('cuil', 11);
            $table->string('nombres', 30);
            $table->string('periodo_vigencia', 6);
            $table->string('telefono', 30);
            $table->string('email', 50);
            $table->string('codigo_postal', 8);
            $table->string('localidad', 50);
            $table->string('provincia', 30);
            $table->string('obra_social_origen', 6);
            $table->string('periodo', 10);
            $table->unsignedBigInteger('id_usuario');
            $table->foreign('id_usuario')->references('cod_usuario')->on('tb_usuarios');
            $table->string('fecha_importacion', 10);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tb_altas_monotributo');
    }
};
