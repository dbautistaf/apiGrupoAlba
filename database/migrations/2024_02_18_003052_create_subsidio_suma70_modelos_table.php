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
        Schema::create('tb_subsidio_suma70', function (Blueprint $table) {
            $table->bigIncrements('id_subsidio_suma70');
            $table->string('cod_obra_soc', 6);
            $table->string('periodo', 6);
            $table->string('cant_benef', 7);            
            $table->string('area_reser', 91);
            $table->decimal('subsidio_total', 15, 2);
            $table->string('periodo_subsidio_suma70', 20);
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
        Schema::dropIfExists('tb_subsidio_suma70');
    }
};
