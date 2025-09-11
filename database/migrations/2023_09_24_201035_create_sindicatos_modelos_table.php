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
        Schema::create('tb_sindicatos', function (Blueprint $table) {
            $table->bigIncrements('cod_sindicato');
            $table->string('apellidos_responsable',150);
            $table->string('avatar_sindicato',150);
            $table->string('correo_responsable',150);
            $table->string('domicilio_sindicato',150);
            $table->boolean('estado')->default(1);
            $table->string('nombres_responsable',50);
            $table->string('nombre_sindicato',50);
            $table->string('telefono_responsable',50);
            $table->string('telefono_sindicato',50);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tb_sindicatos');
    }
};
