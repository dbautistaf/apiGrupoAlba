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
        Schema::create('tb_familiar_comercial', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->char('cuil_titular', 11);                      
            $table->char('cuil_benef', 11);
            $table->string('dni', 15); 
            $table->string('apellidos', 50);            
            $table->string('nombres', 30);
            $table->date('fec_nac');          
            $table->string('nacionalidad', 50);
            $table->string('sexo', 2);            
            $table->boolean('discapacidad');
            $table->char('id_parentesco');
            $table->foreign('id_parentesco')->references('id_parentesco')->on('tb_parentesco');
            $table->char('id_estado_civil');
            $table->foreign('id_estado_civil')->references('id_estado_civil')->on('tb_estado_civil');
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
        Schema::dropIfExists('tb_familiar_comercial');
    }
};
