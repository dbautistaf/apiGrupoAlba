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
        Schema::create('tb_credencial', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('num_carnet',20);
            $table->date('fecha_emision');
            $table->date('fecha_vencimiento');
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
        Schema::dropIfExists('tb_credencial');
    }
};
