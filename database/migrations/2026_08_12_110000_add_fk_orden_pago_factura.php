<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * FK de las OPAs contra las facturas.
     *
     * tb_tes_orden_pago y tb_tes_orden_pago_detalle no tenían NINGUNA foreign key: nada impedía
     * borrar una factura dejando su orden de pago viva y pagable. Así se generaron las OPAs
     * huérfanas relevadas en Alba producción (13 OPAs, $39,2M — ver docs/reporte-danos-opa.md).
     *
     * Ningún endpoint de la app borra facturas físicamente (el botón "Eliminar" en realidad anula),
     * así que el borrado vino de fuera de la aplicación. Por eso la protección tiene que estar en
     * la base: una FK blinda venga de donde venga el DELETE.
     *
     * IMPORTANTE — requiere datos limpios: si quedan OPAs apuntando a facturas inexistentes, la
     * creación de la FK falla con error 1452. Correr ANTES la limpieza de huérfanas.
     * Query para verificar que da 0 en ambas:
     *
     *   SELECT COUNT(*) FROM tb_tes_orden_pago o
     *     LEFT JOIN tb_facturacion_datos f ON f.id_factura = o.id_factura
     *    WHERE o.id_factura IS NOT NULL AND f.id_factura IS NULL;
     *
     *   SELECT COUNT(*) FROM tb_tes_orden_pago_detalle d
     *     LEFT JOIN tb_facturacion_datos f ON f.id_factura = d.id_factura
     *    WHERE d.id_factura IS NOT NULL AND f.id_factura IS NULL;
     *
     * Se usa RESTRICT (comportamiento por defecto), no CASCADE: borrar una factura NO debe borrar
     * su orden de pago en silencio — debe fallar y obligar a anularla explícitamente.
     */
    public function up()
    {
        $huerfanasOpa = DB::selectOne("
            SELECT COUNT(*) AS n FROM tb_tes_orden_pago o
            LEFT JOIN tb_facturacion_datos f ON f.id_factura = o.id_factura
            WHERE o.id_factura IS NOT NULL AND f.id_factura IS NULL")->n;

        $huerfanasDetalle = DB::selectOne("
            SELECT COUNT(*) AS n FROM tb_tes_orden_pago_detalle d
            LEFT JOIN tb_facturacion_datos f ON f.id_factura = d.id_factura
            WHERE d.id_factura IS NOT NULL AND f.id_factura IS NULL")->n;

        if ($huerfanasOpa > 0 || $huerfanasDetalle > 0) {
            throw new \Exception(
                "No se puede crear la FK: hay OPAs huérfanas (cabecera: {$huerfanasOpa}, "
                . "detalle: {$huerfanasDetalle}). Limpiarlas primero — ver docs/reporte-danos-opa.md."
            );
        }

        Schema::table('tb_tes_orden_pago', function (Blueprint $table) {
            $table->foreign('id_factura', 'fk_opa_factura')
                ->references('id_factura')->on('tb_facturacion_datos');
        });

        Schema::table('tb_tes_orden_pago_detalle', function (Blueprint $table) {
            $table->foreign('id_factura', 'fk_opa_detalle_factura')
                ->references('id_factura')->on('tb_facturacion_datos');
        });
    }

    public function down()
    {
        Schema::table('tb_tes_orden_pago', function (Blueprint $table) {
            $table->dropForeign('fk_opa_factura');
        });

        Schema::table('tb_tes_orden_pago_detalle', function (Blueprint $table) {
            $table->dropForeign('fk_opa_detalle_factura');
        });
    }
};
