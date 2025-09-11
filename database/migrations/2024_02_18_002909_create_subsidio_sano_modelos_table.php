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
        Schema::create('tb_subsidio_sano', function (Blueprint $table) {
            $table->bigIncrements('id_subsidio_sano');
            $table->string('tipo_reg', 2);
            $table->string('cuit', 11);
            $table->string('cuil', 11);
            $table->string('codosoc', 6);
            $table->string('periodo', 4);
            $table->decimal('remosimp', 12,2);
            $table->decimal('apobsoc', 12,2);
            $table->decimal('conosoc', 12,2);
            $table->decimal('subsidio', 12,2);
            $table->string('obsocrel', 6);
            $table->string('inpartot', 1);
            $table->string('inddbcr', 1);
            $table->string('motoexcep', 1);
            $table->string('periodo_subsidio_sano',20);
            $table->date('fecha_proceso');
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
        Schema::dropIfExists('tb_subsidio_sano');
    }
};
