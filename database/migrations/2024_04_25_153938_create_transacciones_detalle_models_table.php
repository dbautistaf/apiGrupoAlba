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
        Schema::create('tb_transacciones_detalle', function (Blueprint $table) {
            $table->bigIncrements('id_transacciones_detalle');
            $table->integer('linea');
            $table->integer('registro');
            $table->string('nombre', 250);
            $table->integer('cantidad');
            $table->integer('cobertura');
            $table->decimal('precio_vigente',12, 2);
            $table->unsignedBigInteger('id_transacciones');
            $table->foreign('id_transacciones')->references('id_transacciones')->on('tb_transacciones_validador');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tb_transacciones_detalle');
    }
};
