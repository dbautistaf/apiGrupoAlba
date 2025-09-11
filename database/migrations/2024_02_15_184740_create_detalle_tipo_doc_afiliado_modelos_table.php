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
        Schema::create('tb_detalle_tipo_doc_afiliado', function (Blueprint $table) {
            $table->bigIncrements('id_detalle');
            $table->string('nombre_archivo', 100);        
            $table->unsignedBigInteger('id_padron');
            $table->foreign('id_padron')->references('id')->on('tb_padron');
            $table->unsignedBigInteger('id_tipo_documentacion');
            $table->foreign('id_tipo_documentacion')->references('id_tipo_documentacion')->on('tb_tipo_documentacion_afiliado');
            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tb_detalle_tipo_doc_afiliado');
    }
};
