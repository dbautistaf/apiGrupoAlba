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
        Schema::create('tb_transacciones_validador', function (Blueprint $table) {
            $table->bigIncrements('id_transacciones');
            $table->integer('id_autorizacion');
            $table->date('fecha_receta');
            $table->date('fecha_venta');
            $table->string('plan', 50);
            $table->integer('nro_receta');
            $table->string('cuil', 11);
            $table->string('nombre_afiliado', 250);
            $table->integer('matricula_medico');
            $table->string('nombre_medico', 250);
            $table->string('diagnostico', 150);
            $table->string('nombre_farmacia', 150);
            $table->string('cuit', 11);
            $table->string('localidad', 150);
            $table->date('fecha_carga');           
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
        Schema::dropIfExists('tb_transacciones_validador');
    }
};
