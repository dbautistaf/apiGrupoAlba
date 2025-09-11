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
        Schema::create('tb_transferencias', function (Blueprint $table) {
            $table->bigIncrements('id_transferencia');
            $table->string('organ', 4);
            $table->string('codconc', 3);
            $table->decimal('importe', 15, 2);
            $table->string('inddbcr', 1);
            $table->date('fecproc');
            $table->date('fecrec');
            $table->string('cuitcont', 11);
            $table->string('periodo', 4);
            $table->string('id_tranf', 15);
            $table->string('cuitapo', 11);
            $table->string('banco', 3);
            $table->string('codsuc', 3);
            $table->string('zona', 2);
            $table->string('periodo_tranf', 20);
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
        Schema::dropIfExists('tb_transferencias');
    }
};
