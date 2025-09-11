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
        Schema::create('tb_farmanexus', function (Blueprint $table) {
            $table->bigIncrements('id_farmanexus');
            $table->string('cuf', 10);
            $table->string('cuit', 11);
            $table->string('razon_social', 80);
            $table->string('nombre_fantasia', 80);
            $table->string('provincia', 30);
            $table->dateTime('fecha_validacion');
            $table->string('numero_receta', 20);
            $table->integer('nro_item');
            $table->string('nro_afil', 11);
            $table->string('afiliado', 50);
            $table->integer('edad');
            $table->string('producto', 100);
            $table->integer('cantidad');         
            $table->decimal('precio_venta', 10,2);
            $table->decimal('precio_venta_desc', 10,2);      
            $table->string('cod_validacion', 30);
            $table->string('estado', 100);
            $table->dateTime('fecha_receta');
            $table->string('ppio_activo', 120);
            $table->string('cobertura', 10);
            $table->string('plan', 30);
            $table->string('tipo_matricula', 3);
            $table->string('numero_matricula', 20);
            $table->string('medico', 50)->nullable();
            $table->integer('registroab');
            $table->string('nrodoc_afiliado', 100);
            $table->string('otro_costo', 40)->nullable();
            $table->string('laboratorio', 40);
            $table->integer('labo_id');
            $table->integer('prestador')->nullable();
            $table->string('presentacion_fcia', 30);
            $table->integer('id_externo');
            $table->string('recetario_orig', 20);
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
        Schema::dropIfExists('tb_farmanexus');
    }
};
