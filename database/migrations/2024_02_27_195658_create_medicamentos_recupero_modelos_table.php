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
        Schema::create('tb_medicamentos_recupero', function (Blueprint $table) {
            $table->bigIncrements('id_medicamento_recupero');            
            $table->string('atc', 20);      
            $table->string('generico', 100);      
            $table->string('dosis', 40);
            $table->string('presentacion', 100); 
            $table->decimal('reintegro_por_unidad', 10,2);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tb_medicamentos_recupero');
    }
};
