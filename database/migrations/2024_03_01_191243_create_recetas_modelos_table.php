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
        Schema::create('tb_recetas', function (Blueprint $table) {
            $table->bigIncrements('id_receta');
            $table->integer('numero_receta');
            $table->unsignedBigInteger('id_farmacia');
            $table->foreign('id_farmacia')->references('id_farmacia')->on('tb_farmacias');
            $table->unsignedBigInteger('id_padron');
            $table->foreign('id_padron')->references('id')->on('tb_padron'); 
            $table->text('observaciones')->nullable();  
            $table->date('fecha_receta');
            $table->date('fecha_carga');
            $table->string('medico', 50);
            $table->string('caratula', 50);               
            $table->string('matricula', 20);
            $table->string('colegio', 80);                           
            $table->integer('origen'); 
            $table->decimal('subtotal',10,2);           
            $table->decimal('importe_total',10,2);
            $table->decimal('total_obra_social',10,2);
            $table->decimal('total_afiliado',10,2);
            $table->string('lote',50);            
            $table->string('validado',2);
            $table->unsignedBigInteger('id_tipo_plan');
            $table->date('fecha_prescripcion');
            $table->unsignedBigInteger('periodo'); 
            $table->foreign('periodo')->references('id_periodo')->on('tb_periodo');
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
        Schema::dropIfExists('tb_recetas');
    }
};
