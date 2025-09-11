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
        Schema::create('tb_turnos', function (Blueprint $table) {
            $table->bigIncrements('id_turno');
            $table->date('fecha_desde');
            $table->date('fecha_hasta');
            $table->time('horario_inicio');
            $table->time('horario_fin');
            $table->boolean('estado')->default(1);
            $table->unsignedBigInteger('id_afiliado');
            $table->foreign('id_afiliado')->references('id')->on('tb_padron');
            $table->unsignedBigInteger('id_centro_medico');
            $table->foreign('id_centro_medico')->references('id_centro_medico')->on('tb_centros_medicos');
            $table->unsignedBigInteger('id_medico');
            $table->foreign('id_medico')->references('id_medico')->on('tb_medicos');
            $table->unsignedBigInteger('id_locatorio');
            $table->foreign('id_locatorio')->references('id_locatorio')->on('tb_locatorio');
            $table->unsignedBigInteger('id_especialidad');
            $table->foreign('id_especialidad')->references('id_especialidad')->on('tb_especialidades');
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
        Schema::dropIfExists('tb_turnos');
    }
};
