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
        Schema::create('tb_mesa_entrada', function (Blueprint $table) {
            $table->bigIncrements('cod_mesa');
            $table->unsignedBigInteger('cod_tipo_documentacion');
            $table->foreign('cod_tipo_documentacion')->references('cod_tipo_documentacion')->on('tb_tipo_documentacion');
            $table->string('emisor',150);
            $table->string('nro_factura',20);
            $table->decimal('importe');
            $table->date('fecha_documentacion')->nullable();
            $table->date('fecha_carga');
            $table->text('observaciones')->nullable();
            $table->unsignedBigInteger('cod_tipo_area');
            $table->foreign('cod_tipo_area')->references('cod_tipo_area')->on('tb_tipo_area');
            $table->unsignedBigInteger('cod_sindicato');
            $table->foreign('cod_sindicato')->references('cod_sindicato')->on('tb_sindicatos');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tb_mesa_entrada');
    }
};
