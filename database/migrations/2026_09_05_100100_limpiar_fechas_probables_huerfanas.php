<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Limpia las fechas probables de pago huérfanas y agrega la FK que impide que vuelvan.
 *
 * ═══ El problema ═══
 *
 * `tb_tes_fecha_probable_pago` cuelga de `tb_tes_pago` por `id_pago`, pero nunca tuvo una FK que
 * lo garantice. Cuando se borró una boleta, sus fechas quedaron sueltas apuntando a un `id_pago`
 * que ya no existe.
 *
 * Eso no sería más que basura acumulada si no fuera por el `AUTO_INCREMENT`: cuando se crea una
 * boleta nueva, MySQL le asigna el siguiente id disponible, y si alguna fecha huérfana apuntaba
 * justo a ese número, **la boleta nueva hereda cuotas que no son suyas**. La orden aparece con
 * fechas de pago fantasma, de una orden borrada hace meses.
 *
 * ═══ Cómo se encontró (2026-09-05) ═══
 *
 * `test_cronograma.php` empezó a fallar en OSV: confirmaba una orden con 2 cuotas y el sistema
 * devolvía 3. La tercera era del 2025-11-06, de una boleta borrada cuyo `id_pago` (1293) coincidía
 * con el que le tocó a la boleta nueva.
 *
 * ═══ Estado relevado ═══
 *
 *   alba3 → 0 huérfanas (limpio).
 *   osv2  → 513 huérfanas de 1.070 filas (48%), con id_pago entre 38 y 3353.
 *           479 de ellas apuntan a ids POR ENCIMA del auto_increment (1294): son las que van a
 *           contaminar boletas futuras a medida que el contador avance.
 *
 * Se verificó que ninguna huérfana está referenciada por un abono (`tb_tes_pago_parcial`): son
 * basura pura, no hay nada que rescatar.
 */
return new class extends Migration
{
    public function up(): void
    {
        // 1) Respaldo antes de borrar. Se conserva: es la única copia de esas filas.
        //
        // Se afloja el sql_mode solo para esto: entre las huérfanas de OSV hay 2 filas con
        // `fecha_probable_pago = '0000-00-00'`, y con NO_ZERO_DATE el CREATE ... SELECT las
        // rechaza y no se puede respaldar nada. Ninguna fila viva tiene fechas en cero — es
        // basura confinada a lo que se está por borrar, y el respaldo tiene que guardarlas tal
        // cual están, no "arregladas".
        if (!Schema::hasTable('tb_tes_fecha_probable_huerfanas_bkp_20260905')) {
            $sqlModeOriginal = DB::select('SELECT @@SESSION.sql_mode AS m')[0]->m;

            try {
                DB::statement(
                    "SET SESSION sql_mode = REPLACE(REPLACE(@@SESSION.sql_mode,"
                    . "'NO_ZERO_DATE',''),'NO_ZERO_IN_DATE','')"
                );

                DB::statement('
                    CREATE TABLE tb_tes_fecha_probable_huerfanas_bkp_20260905 AS
                    SELECT fp.*
                    FROM tb_tes_fecha_probable_pago fp
                    LEFT JOIN tb_tes_pago p ON p.id_pago = fp.id_pago
                    WHERE p.id_pago IS NULL
                ');
            } finally {
                DB::statement("SET SESSION sql_mode = '" . addslashes($sqlModeOriginal) . "'");
            }
        }

        // 2) Borrar las huérfanas.
        DB::statement('
            DELETE fp FROM tb_tes_fecha_probable_pago fp
            LEFT JOIN tb_tes_pago p ON p.id_pago = fp.id_pago
            WHERE p.id_pago IS NULL
        ');

        // 3) La FK que impide que el problema vuelva. int(11) CON SIGNO, como el resto de las
        //    tablas viejas — usar UNSIGNED acá rompe con error 1005.
        $tieneFk = count(DB::select(
            "SELECT 1 FROM information_schema.KEY_COLUMN_USAGE
             WHERE TABLE_SCHEMA = DATABASE() AND CONSTRAINT_NAME = 'fk_fpp_pago'"
        )) > 0;

        if (!$tieneFk) {
            DB::statement('ALTER TABLE tb_tes_fecha_probable_pago ADD INDEX idx_fpp_pago (id_pago)');
            DB::statement(
                'ALTER TABLE tb_tes_fecha_probable_pago
                 ADD CONSTRAINT fk_fpp_pago FOREIGN KEY (id_pago)
                   REFERENCES tb_tes_pago (id_pago) ON DELETE CASCADE'
            );
        }
    }

    public function down(): void
    {
        $tieneFk = count(DB::select(
            "SELECT 1 FROM information_schema.KEY_COLUMN_USAGE
             WHERE TABLE_SCHEMA = DATABASE() AND CONSTRAINT_NAME = 'fk_fpp_pago'"
        )) > 0;

        if ($tieneFk) {
            DB::statement('ALTER TABLE tb_tes_fecha_probable_pago DROP FOREIGN KEY fk_fpp_pago');
            DB::statement('ALTER TABLE tb_tes_fecha_probable_pago DROP INDEX idx_fpp_pago');
        }

        // Las filas borradas NO se restauran solas: están en
        // tb_tes_fecha_probable_huerfanas_bkp_20260905 y volver a insertarlas violaría la FK que
        // se acaba de sacar. Si hiciera falta, se restauran a mano desde el respaldo.
    }
};
