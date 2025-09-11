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
        Schema::create('tb_detalle_padron_tipo_plan', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->date('fecha_alta');
            $table->date('fecha_baja');
            $table->unsignedBigInteger('id_tipo_plan');
            $table->foreign('id_tipo_plan')->references('id_tipo_plan')->on('tb_tipo_plan');
            $table->unsignedBigInteger('id_padron');
            $table->foreign('id_padron')->references('id')->on('tb_padron');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tb_detalle_padron_tipo_plan');
    }
};
