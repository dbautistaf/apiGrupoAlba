<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tabla puente OPA <-> Factura, con el monto imputado a cada factura.
     *
     * Hoy la relación vive en tb_tes_orden_pago_detalle, que guarda `monto_factura` (el total
     * de la factura), no cuánto de esa factura cubre la orden. Sin ese dato no se pueden
     * derivar los estados de la OP ni imputar pagos parciales — las dos cosas que pide el
     * Circuito de Pagos a Prestadores (ver docs/circuito-pagos/plan-fase1-pagos.md).
     *
     * El UNIQUE (id_orden_pago, id_factura) es la garantía estructural contra la fila de
     * detalle duplicada: ese bug dejó la OPA de la factura 5239 (OSV) por el doble de su valor.
     *
     * OJO con los tipos: las tablas viejas usan int(11) CON SIGNO. Usar unsigned/foreignId
     * rompe las FKs con error 1005.
     */
    public function up()
    {
        if (Schema::hasTable('tb_tes_opa_factura')) {
            return;
        }

        Schema::create('tb_tes_opa_factura', function (Blueprint $table) {
            $table->increments('id_op_factura');
            $table->integer('id_orden_pago');
            $table->integer('id_factura');
            $table->decimal('monto_aplicado', 18, 2)->default(0.00);
            $table->dateTime('fecha_imputacion')->nullable();
            $table->integer('cod_usuario')->nullable();

            $table->unique(['id_orden_pago', 'id_factura'], 'uq_opa_factura');
            $table->index('id_factura', 'idx_opaf_factura');

            $table->foreign('id_orden_pago', 'fk_opaf_orden_pago')
                ->references('id_orden_pago')->on('tb_tes_orden_pago')
                ->onDelete('restrict');
            $table->foreign('id_factura', 'fk_opaf_factura')
                ->references('id_factura')->on('tb_facturacion_datos')
                ->onDelete('restrict');
        });
    }

    public function down()
    {
        Schema::dropIfExists('tb_tes_opa_factura');
    }
};
