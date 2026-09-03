<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * `id_banco_emisor` se creó en 2026_09_03_100200 como un int suelto, sin FK ni referente.
 * El catálogo real de bancos de este sistema es `tb_tes_entidades_bancarias` (la misma tabla
 * a la que apunta `tb_tes_cuentas_bancarias.id_entidad_bancaria`), así que se ata ahí.
 *
 * Se mantiene como columna propia en vez de derivar siempre de la cuenta bancaria porque el
 * catálogo de cuentas está incompleto (faltan Galicia, ICBC y Nación — ver docs/modulo-conciliaciones.md):
 * hay eCheq que se emiten desde un banco que todavía no tiene cuenta cargada.
 *
 * Ambas columnas son int(11) CON SIGNO, como el resto de las tablas viejas.
 */
return new class extends Migration
{
    public function up(): void
    {
        // La FK falla si quedó algún valor que no existe en el catálogo.
        $huerfanos = DB::table('tb_tes_pago as p')
            ->leftJoin('tb_tes_entidades_bancarias as e', 'e.id_entidad_bancaria', '=', 'p.id_banco_emisor')
            ->whereNotNull('p.id_banco_emisor')
            ->whereNull('e.id_entidad_bancaria')
            ->count();

        if ($huerfanos > 0) {
            throw new \Exception(
                "Hay {$huerfanos} pago(s) con id_banco_emisor que no existe en tb_tes_entidades_bancarias. "
                . 'Corregirlos antes de crear la FK.'
            );
        }

        // `tb_tes_entidades_bancarias` NO tiene primary key ni ningún índice (verificado
        // 2026-09-03). Una FK exige que la columna referenciada esté indexada, así que sin esto
        // el ALTER falla con error 1005 / errno 150.
        $tieneIndice = count(DB::select('SHOW INDEX FROM tb_tes_entidades_bancarias')) > 0;

        if (!$tieneIndice) {
            DB::statement('ALTER TABLE tb_tes_entidades_bancarias ADD PRIMARY KEY (id_entidad_bancaria)');
        }

        Schema::table('tb_tes_pago', function (Blueprint $table) {
            $table->index('id_banco_emisor', 'idx_pago_banco_emisor');
            $table->foreign('id_banco_emisor', 'fk_pago_banco_emisor')
                ->references('id_entidad_bancaria')
                ->on('tb_tes_entidades_bancarias')
                ->onDelete('restrict');
        });
    }

    public function down(): void
    {
        Schema::table('tb_tes_pago', function (Blueprint $table) {
            $table->dropForeign('fk_pago_banco_emisor');
            $table->dropIndex('idx_pago_banco_emisor');
        });
    }
};
