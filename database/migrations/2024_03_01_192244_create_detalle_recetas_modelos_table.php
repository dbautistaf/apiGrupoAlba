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
        Schema::create('tb_recetas_detalle', function (Blueprint $table) {
            $table->bigIncrements('id_detalle_receta');
            $table->unsignedBigInteger('id_vademecum');
            $table->foreign('id_vademecum')->references('id_vademecum')->on('tb_vademecum');
            $table->integer('cantidad');
            $table->decimal('valor_unitario',10,2);
            $table->decimal('valor_total',10,2);
            $table->decimal('afiliado_total',10,2);
            $table->decimal('cargo_osyc',10,2);
            $table->decimal('venta_publico',10,2);
            $table->string('diabetes',1);
            $table->string('recupero',1);
            $table->string('pmi',1);
            $table->unsignedBigInteger('id_receta');
            $table->foreign('id_receta')->references('id_receta')->on('tb_recetas');
            $table->unsignedBigInteger('id_cobertura');
            $table->foreign('id_cobertura')->references('id_cobertura')->on('tb_cobertura'); 
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tb_recetas_detalle');
    }
};
