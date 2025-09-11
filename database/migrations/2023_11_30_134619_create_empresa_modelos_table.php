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
        Schema::create('tb_empresa', function (Blueprint $table) {
            $table->bigIncrements('id_empresa');
            $table->string('razon_social', 70);
            $table->unsignedBigInteger('id_localidad');
            $table->foreign('id_localidad')->references('id_localidad')->on('tb_localidad');
            $table->date('fecha_alta');
            $table->date('fecha_carga');
            $table->unsignedBigInteger('id_usuario');
            $table->foreign('id_usuario')->references('cod_usuario')->on('tb_usuarios');
            $table->string('telefono', 20);
            $table->string('celular', 20);
            $table->string('cuit', 45);
            $table->unsignedBigInteger('id_partido');
            $table->foreign('id_partido')->references('id_partido')->on('tb_partidos');
            $table->unsignedBigInteger('id_provincia');
            $table->foreign('id_provincia')->references('id_provincia')->on('tb_provincias');
            $table->string('nombre_fantasia',70);
            $table->string('email',50);
            $table->date('fecha_baja');
            $table->unsignedBigInteger('id_actividad_economica');
            $table->foreign('id_actividad_economica')->references('id')->on('tb_actividad_economica');
            $table->unsignedBigInteger('id_delegacion');
            $table->foreign('id_delegacion')->references('id')->on('tb_delegacion');
            $table->text('observaciones'); 
            $table->string('domicilio',50);
            $table->string('tipo_empresa', 2);       
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tb_empresa');
    }
};
