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
        Schema::create('tb_obras_sociales', function (Blueprint $table) {
            $table->bigIncrements('id_obra');
            $table->string('rnos', 7);
            $table->string('denominacion',200);
            $table->string('sigla',20);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tb_obras_sociales');
    }
};
