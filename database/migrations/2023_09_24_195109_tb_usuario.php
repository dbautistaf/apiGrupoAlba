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
        Schema::create('tb_usuarios', function (Blueprint $table) {
            $table->bigIncrements('cod_usuario');
            $table->string('nombre_apellidos',200);
            $table->string('documento',20);
            $table->string('telefono',20)->nullable();
            $table->string('direccion',200)->nullable();
            $table->date('fecha_alta');
            $table->date('fecha_baja')->nullable();
            $table->boolean('estado_cuenta')->default(0);
            $table->date('fecha_cambio_clave');
            $table->string('email')->unique();
            $table->timestamp('codigo_verificacion')->nullable();
            $table->string('password');
            $table->unsignedBigInteger('cod_perfil');
            $table->foreign('cod_perfil')->references('cod_perfil')->on('tb_perfiles');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tb_usuarios');
    }
};
