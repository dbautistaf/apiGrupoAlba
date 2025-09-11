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
        Schema::create('tb_declaraciones_juradas', function (Blueprint $table) {
            $table->bigIncrements('id_ddjj'); 
            $table->string('codosoc', 6);
            $table->string('periodo', 4);
            $table->string('cuit', 11);
            $table->string('cuil', 11);
            $table->decimal('remimpo', 12,2);
            $table->decimal('imposad', 8,2);
            $table->string('zona', 2);
            $table->string('grpfam', 2);
            $table->string('nogrpfam', 2);
            $table->string('secoblig', 3);
            $table->string('condicion', 2);
            $table->string('situacion', 2);
            $table->string('actividad', 3);
            $table->string('modalidad', 3);
            $table->string('codsini', 2);
            $table->decimal('apadios', 8,2);
            $table->string('version', 2);
            $table->decimal('rem5', 11,2);
            $table->string('esposa', 1);
            $table->decimal('excosapo', 12,2);
            $table->string('indret', 1);
            $table->string('indexccon', 1);
            $table->date('fecpresent');
            $table->date('fecproc');
            $table->string('origrect', 1);
            $table->string('filler', 34);
            $table->decimal('remcont', 11,2);
            $table->string('release_ver', 2);
            $table->string('periodo_ddjj',20);
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
        Schema::dropIfExists('tb_declaraciones_juradas');
    }
};
