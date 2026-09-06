<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Mueve la CUENTA DE ORIGEN del pago al abono.
 *
 * ═══ Por qué ═══
 *
 * Cada abono (`tb_tes_pago_parcial`) ya tenía lo suyo: `id_forma_pago` y `id_banco_emisor`. Pero
 * la **cuenta de origen** vivía una tabla más arriba, en la boleta (`tb_tes_pago`), o sea una
 * sola para toda la orden. Era el único de los tres que había quedado a nivel boleta.
 *
 * Consecuencia concreta, reportada el 2026-09-06: no se puede pagar una orden con dos
 * transferencias desde bancos distintos. El modal de Confirmar Pago ofrece un único selector de
 * "Cuenta Bancaria de Origen" porque el modelo no tiene dónde guardar más de una — y además
 * vuelve a preguntar el banco de un eCheq que ya lo tenía definido.
 *
 * Es el mismo arrastre de cuando una orden = un pago, igual que la forma de pago que se sacaba
 * de "Confirmar OPA" (ver punto 13 de plan-fase1-pagos.md).
 *
 * ═══ Qué hace ═══
 *
 * 1. Agrega `id_cuenta_bancaria` a `tb_tes_pago_parcial`, nullable.
 * 2. Backfillea los abonos existentes con la cuenta de su boleta, para que nada pierda el dato
 *    (relevado en local: 293 de 300 abonos tienen cuenta en la boleta).
 * 3. FK contra `tb_tes_cuentas_bancarias`. `int(11)` CON SIGNO, como el resto de las tablas
 *    viejas — UNSIGNED rompe con error 1005.
 *
 * `tb_tes_pago.id_cuenta_bancaria` **NO se toca**: la siguen usando el circuito viejo de
 * confirmación y las pantallas de pagos. Se deprecia después, cuando todo lea del abono.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('tb_tes_pago_parcial', 'id_cuenta_bancaria')) {
            DB::statement(
                'ALTER TABLE tb_tes_pago_parcial
                 ADD COLUMN id_cuenta_bancaria int(11) NULL AFTER id_banco_emisor'
            );
        }

        // Backfill: cada abono hereda la cuenta que tenía su boleta.
        //
        // El JOIN contra `tb_tes_cuentas_bancarias` no es decorativo: hay boletas apuntando a
        // cuentas que ya no existen (1 en Alba, 0 en OSV al 2026-09-06) y copiar esa referencia
        // rota hace fallar la FK de abajo con error 1452. Esos abonos quedan con la cuenta en
        // NULL; el dato original sigue intacto en `tb_tes_pago.id_cuenta_bancaria`, no se pierde
        // nada — simplemente no se arrastra una referencia que no lleva a ningún lado.
        DB::statement('
            UPDATE tb_tes_pago_parcial pp
            JOIN tb_tes_pago p ON p.id_pago = pp.id_pago
            JOIN tb_tes_cuentas_bancarias c ON c.id_cuenta_bancaria = p.id_cuenta_bancaria
            SET pp.id_cuenta_bancaria = p.id_cuenta_bancaria
            WHERE pp.id_cuenta_bancaria IS NULL
        ');

        // Por si una corrida anterior alcanzó a copiar alguna referencia rota antes de este
        // arreglo: se limpian para que la FK pueda crearse.
        DB::statement('
            UPDATE tb_tes_pago_parcial pp
            LEFT JOIN tb_tes_cuentas_bancarias c ON c.id_cuenta_bancaria = pp.id_cuenta_bancaria
            SET pp.id_cuenta_bancaria = NULL
            WHERE pp.id_cuenta_bancaria IS NOT NULL AND c.id_cuenta_bancaria IS NULL
        ');

        // El índice y la FK se chequean por separado: si la FK falla (por ejemplo con 1452 por
        // una referencia rota), el índice ya quedó creado y reintentar la migración explotaba con
        // 1061 "Duplicate key name".
        $tieneIndice = count(DB::select(
            "SELECT 1 FROM information_schema.STATISTICS
             WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'tb_tes_pago_parcial'
               AND INDEX_NAME = 'idx_pp_cuenta_bancaria'"
        )) > 0;

        if (!$tieneIndice) {
            DB::statement('ALTER TABLE tb_tes_pago_parcial ADD INDEX idx_pp_cuenta_bancaria (id_cuenta_bancaria)');
        }

        $tieneFk = count(DB::select(
            "SELECT 1 FROM information_schema.KEY_COLUMN_USAGE
             WHERE TABLE_SCHEMA = DATABASE() AND CONSTRAINT_NAME = 'fk_pp_cuenta_bancaria'"
        )) > 0;

        if (!$tieneFk) {
            DB::statement(
                'ALTER TABLE tb_tes_pago_parcial
                 ADD CONSTRAINT fk_pp_cuenta_bancaria FOREIGN KEY (id_cuenta_bancaria)
                   REFERENCES tb_tes_cuentas_bancarias (id_cuenta_bancaria) ON DELETE RESTRICT'
            );
        }
    }

    public function down(): void
    {
        $tieneFk = count(DB::select(
            "SELECT 1 FROM information_schema.KEY_COLUMN_USAGE
             WHERE TABLE_SCHEMA = DATABASE() AND CONSTRAINT_NAME = 'fk_pp_cuenta_bancaria'"
        )) > 0;

        if ($tieneFk) {
            DB::statement('ALTER TABLE tb_tes_pago_parcial DROP FOREIGN KEY fk_pp_cuenta_bancaria');
            DB::statement('ALTER TABLE tb_tes_pago_parcial DROP INDEX idx_pp_cuenta_bancaria');
        }

        if (Schema::hasColumn('tb_tes_pago_parcial', 'id_cuenta_bancaria')) {
            DB::statement('ALTER TABLE tb_tes_pago_parcial DROP COLUMN id_cuenta_bancaria');
        }
    }
};
