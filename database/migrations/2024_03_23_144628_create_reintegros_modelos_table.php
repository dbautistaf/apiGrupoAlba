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
        Schema::create('tb_reintegros', function (Blueprint $table) {
            $table->bigIncrements('nro_reintegro');
            $table->date('fecha_solicitud');
            $table->string('url_adjunto', 100);
            $table->string('motivo', 100);
            $table->decimal('importe_solicitado',20,2);
            $table->decimal('importe_reconocido',20,2);
            $table->string('autorizado_por', 50);
            $table->text('observaciones')->nullable();
            $table->string('cbu_prestador', 100);
            $table->string('nro_factura', 30);
            $table->string('fecha_carga', 30); 
            $table->string('nombre_prestador', 100);
            $table->string('estado', 10);  
            $table->integer('cantidad');       
            $table->text('observaciones_auditoria')->nullable();   
            $table->date('fecha_transferencia');
            $table->unsignedBigInteger('id_usuario');
            $table->foreign('id_usuario')->references('cod_usuario')->on('tb_usuarios');
            $table->unsignedBigInteger('id_filial');
            $table->foreign('id_filial')->references('id_filial')->on('tb_filial');
            $table->unsignedBigInteger('id_afiliados');
            $table->foreign('id_afiliados')->references('id')->on('tb_padron');
            $table->unsignedBigInteger('id_estado_autorizacion');
            $table->foreign('id_estado_autorizacion')->references('id_estado_autorizacion')->on('tb_estado_autorizacion');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tb_reintegros');
    }
};
