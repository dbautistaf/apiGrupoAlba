<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Trazabilidad de anulación/reemisión (punto 7) y tipo de orden (punto 8).
     *
     * `id_opa_reemplazada`: cuando se anula una orden y se emite otra por las mismas facturas,
     * queda el vínculo entre las dos. Hoy no existe — ni acá ni en ospf — y sin eso no hay forma
     * de reconstruir por qué hay dos órdenes para el mismo conjunto de facturas. Es un dato que
     * pide auditoría.
     *
     * `tipo_opa`: NORMAL / ANTICIPO / APLICACION. Los anticipos son fase 3, pero la columna se
     * agrega ahora para no volver a tocar una tabla de 4.188 (Alba) y 4.643 (OSV) registros.
     * Default NORMAL, así todo lo existente queda bien clasificado sin migrar nada.
     *
     * OJO: ya existe un campo `anticipo` en tb_tes_pago, pero significa OTRA cosa — pagar por
     * adelantado parte de una orden que YA tiene facturas. El anticipo del requerimiento es una
     * orden SIN facturas con saldo a aplicar después. Son conceptos distintos con el mismo
     * nombre; no confundirlos.
     */
    public function up()
    {
        Schema::table('tb_tes_orden_pago', function (Blueprint $table) {
            if (!Schema::hasColumn('tb_tes_orden_pago', 'id_opa_reemplazada')) {
                $table->integer('id_opa_reemplazada')->nullable()->after('cod_usuario_rechaza');
            }
            if (!Schema::hasColumn('tb_tes_orden_pago', 'tipo_opa')) {
                $table->string('tipo_opa', 15)->default('NORMAL')->after('tipo_factura');
            }
        });

        $yaExiste = collect(Schema::getConnection()
            ->select("SHOW INDEX FROM tb_tes_orden_pago WHERE Key_name = 'fk_opa_reemplazada'"))
            ->isNotEmpty();

        if (!$yaExiste) {
            Schema::table('tb_tes_orden_pago', function (Blueprint $table) {
                $table->foreign('id_opa_reemplazada', 'fk_opa_reemplazada')
                    ->references('id_orden_pago')->on('tb_tes_orden_pago')
                    ->onDelete('restrict');
            });
        }
    }

    public function down()
    {
        Schema::table('tb_tes_orden_pago', function (Blueprint $table) {
            $table->dropForeign('fk_opa_reemplazada');
            $table->dropColumn(['id_opa_reemplazada', 'tipo_opa']);
        });
    }
};
