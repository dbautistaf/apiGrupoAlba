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
        Schema::create('tb_padron_comercial', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('id_empresa');
            $table->foreign('id_empresa')->references('id_empresa')->on('tb_empresa');
            $table->char('cuil_tit', 11);
            $table->char('id_tipo_documento');
            $table->foreign('id_tipo_documento')->references('id_tipo_documento')->on('tb_tipo_documentos');
            $table->integer('dni');
            $table->string('nombre', 30);
            $table->string('apellidos', 50);
            $table->char('id_sexo');
            $table->foreign('id_sexo')->references('id_sexo')->on('tb_sexo');
            $table->char('id_estado_civil');
            $table->foreign('id_estado_civil')->references('id_estado_civil')->on('tb_estado_civil');
            $table->date('fe_nac');
            $table->unsignedBigInteger('id_nacionalidad');
            $table->foreign('id_nacionalidad')->references('id_nacionalidad')->on('tb_nacionalidad');
            $table->string('calle', 50);
            $table->string('numero', 50);
            $table->string('piso', 50)->nullable();
            $table->string('depto', 50)->nullable();
            $table->unsignedBigInteger('id_localidad');
            $table->foreign('id_localidad')->references('id_localidad')->on('tb_localidad');
            $table->unsignedBigInteger('id_partido');
            $table->foreign('id_partido')->references('id_partido')->on('tb_partidos');
            $table->unsignedBigInteger('id_provincia');
            $table->foreign('id_provincia')->references('id_provincia')->on('tb_provincias');
            $table->string('telefono', 20);
            $table->date('fe_alta');
            $table->unsignedBigInteger('id_usuario');
            $table->foreign('id_usuario')->references('cod_usuario')->on('tb_usuarios');
            $table->date('fecha_carga');
            $table->char('id_tipo_beneficiario');
            $table->foreign('id_tipo_beneficiario')->references('id_tipo_beneficiario')->on('tb_beneficiario');
            $table->unsignedBigInteger('id_tipo_domicilio');
            $table->foreign('id_tipo_domicilio')->references('id_tipo_domicilio')->on('tb_tipo_domicilio');
            $table->string('email', 45);
            $table->string('celular', 45);
            $table->date('fe_baja');
            $table->integer('activo');
            $table->string('id_cpostal', 6);
            $table->text('observaciones')->nullable();
            $table->unsignedBigInteger('id_tipo_carpeta');
            $table->foreign('id_tipo_carpeta')->references('id_tipo_carpeta')->on('tb_tipo_carpeta');
            $table->unsignedBigInteger('id_qr');
            $table->foreign('id_qr')->references('id_qr')->on('tb_tipo_qr');
            $table->unsignedBigInteger('id_supervisor');
            $table->foreign('id_supervisor')->references('id_supervisor')->on('tb_supervisores');
            $table->unsignedBigInteger('id_agente');
            $table->foreign('id_agente')->references('id_agente')->on('tb_agentes');
            $table->unsignedBigInteger('id_gerente');
            $table->foreign('id_gerente')->references('id_gerente')->on('tb_gerentes');
            $table->unsignedBigInteger('id_gestoria');
            $table->foreign('id_gestoria')->references('id_gestoria')->on('tb_gestoria');
            $table->unsignedBigInteger('id_regimen');
            $table->foreign('id_regimen')->references('id_regimen')->on('tb_regimen');
            $table->decimal('aporte', 11, 2);
            $table->string('clave_fiscal', 50);
            $table->string('tramite', 40);
            $table->unsignedBigInteger('obra_social_proc');
            $table->unsignedBigInteger('obra_social_destino');
            $table->text('observaciones_auditoria')->nullable();            
            $table->unsignedBigInteger('id_estado_autorizacion');         
            $table->unsignedBigInteger('id_locatorio');
            $table->string('numero_form');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tb_padron_comercial');
    }
};
