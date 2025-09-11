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
        Schema::create('tb_nacionalidad', function (Blueprint $table) {
            $table->bigIncrements('id_nacionalidad');
            $table->string('Nombre',45);
            $table->string('Gentilicio',45);
            $table->string('CodNac',45);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tb_nacionalidad');
    }
};
