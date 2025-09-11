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
        Schema::create('tb_menu_acceso_usuario', function (Blueprint $table) {
            $table->bigIncrements('cod_acceso');
            $table->unsignedBigInteger('cod_menu');
            $table->foreign('cod_menu')->references('cod_menu')->on('tb_menus');
            $table->unsignedBigInteger('cod_perfil');
            $table->foreign('cod_perfil')->references('cod_perfil')->on('tb_perfiles');
            $table->boolean('estado_acceso');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tb_menu_acceso_usuario');
    }
};
