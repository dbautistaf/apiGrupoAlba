<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Cuenta contable de contraparte (prestadores / proveedores) por razón social.
     *
     * Antes estaba hardcodeado en AsientoContableRepository (35 prestador / 36 proveedor),
     * lo que solo servía para Alba. No se puede resolver con un match() por id_razon en PHP
     * porque Alba y OSV son bases distintas donde id_razon = 1 significa empresas distintas
     * con cuentas distintas; además los id_detalle_plan son autoincrementales propios de cada
     * base. Por eso el mapeo vive en la base: cada deployment carga sus propios valores.
     */
    public function up()
    {
        if (Schema::hasTable('tb_cont_razon_cuenta_contable')) {
            return;
        }

        Schema::create('tb_cont_razon_cuenta_contable', function (Blueprint $table) {
            $table->increments('id_razon_cuenta_contable');
            $table->integer('id_razon');
            $table->string('tipo_contraparte', 20); // PRESTADOR | PROVEEDOR
            $table->integer('id_detalle_plan');
            $table->boolean('vigente')->default(1);
            $table->integer('cod_usuario')->nullable();
            $table->dateTime('fecha_registra')->nullable();
            $table->integer('cod_usuario_modifica')->nullable();
            $table->dateTime('fecha_modifica')->nullable();

            $table->unique(['id_razon', 'tipo_contraparte'], 'uk_razon_tipo_contraparte');
            $table->foreign('id_razon')->references('id_razon')->on('tb_razones_sociales');
            $table->foreign('id_detalle_plan')->references('id_detalle_plan')->on('tb_cont_planes_cuentas_detalle');
        });
    }

    public function down()
    {
        Schema::dropIfExists('tb_cont_razon_cuenta_contable');
    }
};
