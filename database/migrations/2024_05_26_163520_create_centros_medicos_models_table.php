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
        Schema::create('tb_centros_medicos', function (Blueprint $table) {
            $table->bigIncrements('id_centro_medico');
            $table->string('nombre', 50);
            $table->date('fecha_alta');
            $table->date('fecha_baja')->nullable();
            $table->text('observaciones')->nullable();
            $table->string('responsable', 50);
            $table->string('email', 50);
            $table->string('celular', 30);
            $table->string('telefono', 30);
            $table->boolean('activo')->default(1);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tb_centros_medicos');
    }
};
