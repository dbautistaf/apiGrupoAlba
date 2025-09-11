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
        Schema::create('tb_escolaridad', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('nivel_estudio',50);
            $table->date('fecha_presentacion');
            $table->date('fecha_vencimiento');
            $table->unsignedBigInteger('id_padron');
            $table->foreign('id_padron')->references('id')->on('tb_padron');
        });
    }

    /**
     * Reverse the migrations
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tb_escolaridad');
    }
};
