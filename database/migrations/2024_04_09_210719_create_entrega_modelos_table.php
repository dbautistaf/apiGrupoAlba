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
        Schema::create('tb_entrega', function (Blueprint $table) {
            $table->bigIncrements('id_entrega');
            $table->integer('num_caja'); 
            $table->dateTime('fecha_entrega'); 
            $table->text('observaciones')->nullable();    
            $table->string('personal_recibe', 80);      
            $table->unsignedBigInteger('id_usuario');
            $table->foreign('id_usuario')->references('cod_usuario')->on('tb_usuarios');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tb_entrega');
    }
};
