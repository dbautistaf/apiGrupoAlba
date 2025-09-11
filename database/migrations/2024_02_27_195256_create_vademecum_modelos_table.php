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
        Schema::create('tb_vademecum', function (Blueprint $table) {
            $table->bigIncrements('id_vademecum');            
            $table->string('troquel', 50);
            $table->string('registro', 10);
            $table->string('nombre', 50);
            $table->string('presentacion', 50);
            $table->string('laboratorio', 50);
            $table->string('droga', 50);
            $table->string('accion', 50);
            $table->string('acargo_ospf', 10);            
            $table->string('autorizacion_previa', 2);
            $table->boolean('activo')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tb_vademecum');
    }
};
