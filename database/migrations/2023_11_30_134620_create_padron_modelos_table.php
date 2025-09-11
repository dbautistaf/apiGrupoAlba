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
        Schema::create('tb_padron', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->char('cuil_tit',11);
            $table->char('cuil_benef',11);
            $table->char('id_tipo_documento');
            $table->foreign('id_tipo_documento')->references('id_tipo_documento')->on('tb_tipo_documentos');
            $table->integer('dni');
            $table->string('nombre',30);
            $table->string('apellidos',50);
            $table->char('id_sexo');
            $table->foreign('id_sexo')->references('id_sexo')->on('tb_sexo');
            $table->char('id_estado_civil');
            $table->foreign('id_estado_civil')->references('id_estado_civil')->on('tb_estado_civil');
            $table->date('fe_nac');
            $table->unsignedBigInteger('id_nacionalidad');            
            $table->foreign('id_nacionalidad')->references('id_nacionalidad')->on('tb_nacionalidad');
            $table->string('calle', 50);
            $table->string('numero', 50);
            $table->string('piso', 50);
            $table->string('depto', 50);
            $table->unsignedBigInteger('id_localidad');
            $table->foreign('id_localidad')->references('id_localidad')->on('tb_localidad');
            $table->unsignedBigInteger('id_partido');
            $table->foreign('id_partido')->references('id_partido')->on('tb_partidos');
            $table->unsignedBigInteger('id_provincia');
            $table->foreign('id_provincia')->references('id_provincia')->on('tb_provincias');
            $table->string('telefono',20);
            $table->date('fe_alta');
            $table->unsignedBigInteger('id_usuario');
            $table->foreign('id_usuario')->references('cod_usuario')->on('tb_usuarios');
            $table->date('fecha_carga');
            $table->char('id_tipo_beneficiario');
            $table->foreign('id_tipo_beneficiario')->references('id_tipo_beneficiario')->on('tb_beneficiario');
            $table->char('id_situacion_de_revista');
            $table->foreign('id_situacion_de_revista')->references('id_situacion_de_revista')->on('tb_situacion_revista');
            $table->unsignedBigInteger('id_tipo_domicilio');
            $table->foreign('id_tipo_domicilio')->references('id_tipo_domicilio')->on('tb_tipo_domicilio');
            $table->char('id_parentesco');
            $table->foreign('id_parentesco')->references('id_parentesco')->on('tb_parentesco');
            $table->string('email', 45);
            $table->string('celular', 45);
            $table->date('fe_baja');
            $table->integer('activo');
            $table->unsignedBigInteger('id_estado_super');
            $table->foreign('id_estado_super')->references('id_estado_super')->on('tb_estado_super');
            $table->string('id_cpostal', 6);
            $table->text('observaciones');
            $table->unsignedBigInteger('id_delegacion');
            $table->foreign('id_delegacion')->references('id')->on('tb_delegacion');
            $table->string('domicilio_postal', 100)->nullable();
            $table->string('domicilio_laboral', 100)->nullable();
            $table->unsignedBigInteger('id_locatorio');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tb_padron');
    }
};
