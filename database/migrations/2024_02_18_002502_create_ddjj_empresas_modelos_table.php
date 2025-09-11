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
        Schema::create('tb_ddjj_empresas', function (Blueprint $table) {
            $table->bigIncrements('id_ddjj_empresa');
            $table->string('cuit',11);
            $table->string('nombre_empresa',50);
            $table->string('calle',20);
            $table->string('numero',7);
            $table->string('piso',5);
            $table->string('localidad',20);
            $table->string('cod_prov',3);
            $table->string('cp',8);
            $table->string('cod_os',6);
            $table->string('periodo', 20);
            $table->date('fecha_proceso');
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
        Schema::dropIfExists('tb_ddjj_empresas');
    }
};
