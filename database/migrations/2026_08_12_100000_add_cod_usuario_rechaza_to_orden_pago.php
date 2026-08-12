<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * tb_tes_orden_pago ya guardaba motivo_rechazo y fecha_rechazo, pero no QUIÉN rechazó
     * (cod_usuario es el que creó la OPA). Se agrega para trazabilidad de la anulación,
     * que ahora puede dispararse en cascada al anular una factura.
     */
    public function up()
    {
        if (Schema::hasColumn('tb_tes_orden_pago', 'cod_usuario_rechaza')) {
            return;
        }

        Schema::table('tb_tes_orden_pago', function (Blueprint $table) {
            $table->integer('cod_usuario_rechaza')->nullable()->after('fecha_rechazo');
        });
    }

    public function down()
    {
        Schema::table('tb_tes_orden_pago', function (Blueprint $table) {
            $table->dropColumn('cod_usuario_rechaza');
        });
    }
};
