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
        Schema::create('tb_menus', function (Blueprint $table) {
            $table->bigIncrements('cod_menu');
            $table->string('menu_descripcion',50);
            $table->string('menu_icono',30)->nullable();
            $table->string('menu_link',50)->nullable();
            $table->string('menu_grupo',10)->nullable();
            $table->string('menu_principal',50)->nullable();
            $table->integer('menu_orden')->nullable();
            $table->boolean('menu_estado')->default(1);
            $table->string('tipo_ruta',50)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tb_menus');
    }
};
