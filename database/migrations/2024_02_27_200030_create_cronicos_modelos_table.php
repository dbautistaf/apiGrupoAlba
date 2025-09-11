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
        Schema::create('tb_cronicos', function (Blueprint $table) {
            $table->bigIncrements('id_cronico');
            $table->unsignedBigInteger('id_patologia');
            $table->foreign('id_patologia')->references('id_patologia')->on('tb_patalogia');
            $table->text('observaciones')->nullable();      
            $table->date('fecha_alta');
            $table->date('fecha_baja');
            $table->date('fecha_carga');          
            $table->unsignedBigInteger('id_usuario');
            $table->foreign('id_usuario')->references('cod_usuario')->on('tb_usuarios');
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
        Schema::dropIfExists('tb_cronicos');
    }
};
