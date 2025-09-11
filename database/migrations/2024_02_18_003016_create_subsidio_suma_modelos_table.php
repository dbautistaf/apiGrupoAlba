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
        Schema::create('tb_subsidio_suma', function (Blueprint $table) {
            $table->bigIncrements('id_subsidio_suma');
            $table->string('cod_obra_soc', 6);
            $table->string('periodo', 6);
            $table->string('cant_benef', 7);
            $table->decimal('importe', 15, 2);
            $table->decimal('capita', 15, 2);
            $table->decimal('art2_inca', 15, 2);
            $table->decimal('art2_incb', 15, 2);
            $table->decimal('art2_incc', 15, 2);
            $table->decimal('art3_ajuste', 16, 2);
            $table->decimal('subsidio_total', 16, 2);
            $table->string('periodo_subsidio_suma', 20);
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
        Schema::dropIfExists('tb_subsidio_suma');
    }
};
