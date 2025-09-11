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
        Schema::create('tb_familiares_monotributo', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('obra_social', 6)->nullable();
            $table->string('cuit_titular', 13)->nullable();
            $table->string('tipo_documento_fam', 2)->nullable();
            $table->string('nro_documento_fam', 11)->nullable();
            $table->string('apellido_fam', 50)->nullable();
            $table->string('nombres_fam', 50)->nullable();
            $table->string('parentesco_fam', 2)->nullable();
            $table->string('fecha_alta_fam', 10)->nullable();
            $table->unsignedBigInteger('id_usuario');
            $table->foreign('id_usuario')->references('cod_usuario')->on('tb_usuarios');
            $table->string('periodo_importacion', 10);
            $table->string('fecha_importacion', 10);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tb_familiares_monotributo');
    }
};
