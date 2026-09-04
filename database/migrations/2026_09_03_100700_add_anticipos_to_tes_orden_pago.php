<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Anticipos (punto 8): pagarle a un prestador SIN factura, que ese dinero quede a favor, y
 * aplicarlo después a las facturas que lleguen.
 *
 * ═══ OJO: "anticipo" ya existe en el código y significa OTRA COSA ═══
 *
 * `tb_tes_pago.anticipo` y `monto_anticipado` son un concepto viejo: pagar por adelantado una
 * parte de una OPA **que ya tiene facturas**, generando después una segunda boleta por el saldo.
 * NO se toca ni se reutiliza — sigue funcionando como está.
 *
 * El anticipo del requerimiento se modela con `tipo_opa`:
 *
 *   NORMAL      la OP de siempre, con sus facturas
 *   REEMPLAZO   nació de anular otra (punto 7)
 *   ANTICIPO    sin facturas; cuando se paga, genera saldo a favor del prestador
 *   APLICACION  consume saldo de un ANTICIPO e imputa a facturas. NO genera pago nuevo:
 *               la plata ya salió cuando se pagó el anticipo.
 *
 * `id_opa_anticipo` es el vínculo de la APLICACION hacia su ANTICIPO. Es una columna aparte de
 * `id_opa_reemplazada` a propósito: son dos relaciones distintas, y mezclarlas haría que el
 * recorrido de la cadena de reemplazos se meta por ramas que no son suyas.
 *
 * Todo `int(11)` CON SIGNO, como el resto de las tablas viejas.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tb_tes_orden_pago', function (Blueprint $table) {
            if (!Schema::hasColumn('tb_tes_orden_pago', 'id_opa_anticipo')) {
                $table->integer('id_opa_anticipo')->nullable()->after('id_opa_reemplazada');
                $table->index('id_opa_anticipo', 'idx_opa_anticipo');
                $table->foreign('id_opa_anticipo', 'fk_opa_anticipo')
                    ->references('id_orden_pago')
                    ->on('tb_tes_orden_pago')
                    ->onDelete('restrict');
            }
        });

        // `monto_anticipado` es decimal(10,2): tope de $99.999.999,99. En estas bases ya hay
        // órdenes de $63M, así que un anticipo grande la desbordaría. El resto de las columnas
        // de dinero del módulo son decimal(18,2); se la alinea. (2026-09-03)
        DB::statement('ALTER TABLE tb_tes_orden_pago MODIFY monto_anticipado decimal(18,2) NOT NULL DEFAULT 0.00');

        // Las OPs existentes son todas NORMAL; el default deja consistente lo que venga después.
        DB::statement("UPDATE tb_tes_orden_pago SET tipo_opa = 'NORMAL' WHERE tipo_opa IS NULL OR tipo_opa = ''");
        DB::statement("ALTER TABLE tb_tes_orden_pago MODIFY tipo_opa varchar(15) NOT NULL DEFAULT 'NORMAL'");
    }

    public function down(): void
    {
        Schema::table('tb_tes_orden_pago', function (Blueprint $table) {
            if (Schema::hasColumn('tb_tes_orden_pago', 'id_opa_anticipo')) {
                $table->dropForeign('fk_opa_anticipo');
                $table->dropIndex('idx_opa_anticipo');
                $table->dropColumn('id_opa_anticipo');
            }
        });

        DB::statement('ALTER TABLE tb_tes_orden_pago MODIFY monto_anticipado decimal(10,2) NOT NULL DEFAULT 0.00');
    }
};
