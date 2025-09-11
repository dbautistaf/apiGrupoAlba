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
        Schema::create('tb_expedientes', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('rnos', 6);
            $table->string('cuil_tit', 11);
            $table->string('nombres', 30);
            $table->string('cod_mov', 2);
            $table->string('movimiento', 40);
            $table->string('fecha_vigencia', 10);
            $table->string('expediente', 30);
            $table->string('año_expediente', 4);
            $table->string('tipo_disposicion', 20);
            $table->string('disposicion', 30);
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
        Schema::dropIfExists('tb_expedientes');
    }
};
