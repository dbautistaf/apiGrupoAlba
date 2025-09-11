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
        Schema::create('tb_discapaciodad', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('id_tipo_discapacidad');
            $table->foreign('id_tipo_discapacidad')->references('id_tipo_discapacidad')->on('tb_tipo_discapacidad');
            $table->string('diagnostico',50);
            $table->string('certificado',50);
            $table->date('fecha_certificado');
            $table->date('fecha_vto');
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
        Schema::dropIfExists('tb_discapaciodad');
    }
};
