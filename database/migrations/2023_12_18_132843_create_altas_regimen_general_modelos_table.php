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
        Schema::create('tb_altas_regimen_general', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('cuil_titular', 13);
            $table->string('nombres', 50);
            $table->string('fecha_vigencia', 10);
            $table->string('telefono', 20);
            $table->string('email', 50);
            $table->string('codigo_postal', 4);
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
        Schema::dropIfExists('tb_altas_regimen_general');
    }
};
