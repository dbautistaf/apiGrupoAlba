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
        Schema::create('tb_medicos', function (Blueprint $table) {
            $table->bigIncrements('id_medico');
            $table->string('universidad', 50);
            $table->string('nombre', 30);
            $table->string('cuit', 11);
            $table->string('matricula_nacional', 20);
            $table->string('matricula_provincial', 20);
            $table->string('tipo_matricula', 20);
            $table->date('fecha_alta');
            $table->date('fecha_baja');
            $table->string('email', 30);
            $table->string('celular', 20);
            $table->text('observaciones')->nullable();
            $table->boolean('activo')->default(1);
            $table->unsignedBigInteger('id_especialidad');
            $table->foreign('id_especialidad')->references('id_especialidad')->on('tb_especialidades');
            $table->unsignedBigInteger('id_tipo_entidad');
            $table->foreign('id_tipo_entidad')->references('id_tipo_entidad')->on('tb_tipo_entidad');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tb_medicos');
    }
};
