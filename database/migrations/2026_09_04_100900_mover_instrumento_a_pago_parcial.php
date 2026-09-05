<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * CORRECCIÓN DE DISEÑO: el instrumento (eCheq) vive en el ABONO, no en la boleta de pago.
 *
 * La migración 2026_09_03_100200 puso las columnas del eCheq en `tb_tes_pago`. Estaba un nivel
 * demasiado arriba. La estructura real del sistema es:
 *
 *     Orden de pago (tb_tes_orden_pago)
 *      └── 1 boleta de pago (tb_tes_pago)
 *           ├── N fechas probables (tb_tes_fecha_probable_pago)  — el cronograma
 *           └── N abonos          (tb_tes_pago_parcial)          — CADA CHEQUE VA ACÁ
 *
 * Verificado contra los datos el 2026-09-04:
 *
 *   - **0** OPAs tienen más de una boleta (sobre 4.187)
 *   - **23** boletas tienen más de un abono (sobre 325)
 *   - **81** números de cheque están en `tb_tes_pago_parcial`, contra 9 en `tb_tes_pago`
 *
 * Con el diseño anterior, dos eCheq de una misma orden habrían exigido dos boletas — algo que no
 * ocurre en ningún caso real y que la propia guarda de `getCrearPago` impide.
 *
 * ═══ Se puede hacer sin migrar datos ═══
 *
 * Las cuatro columnas tenían **0 filas con dato** al momento de este cambio: nada se había
 * cargado todavía por el circuito nuevo, y nada se desplegó a producción. Por eso se mueven sin
 * traspaso de información.
 */
return new class extends Migration
{
    public function up(): void
    {
        // 1) Las columnas del instrumento, ahora en el abono.
        foreach ([
            'id_estado_instrumento' => 'int(11) NULL',
            'numero_echeq'          => 'varchar(50) NULL',
            'fecha_emision_echeq'   => 'date NULL',
            'id_banco_emisor'       => 'int(11) NULL',
            'motivo_rechazo'        => 'text NULL',
            'fecha_rechazo'         => 'datetime NULL',
        ] as $columna => $tipo) {
            if (!Schema::hasColumn('tb_tes_pago_parcial', $columna)) {
                DB::statement("ALTER TABLE tb_tes_pago_parcial ADD COLUMN {$columna} {$tipo}");
            }
        }

        // 1.bis) `fecha_confirma_pago` era NOT NULL. Un abono creado por el circuito nuevo NO
        //        nace confirmado — se confirma cuando el banco acredita — y al rechazarlo hay que
        //        poder vaciarla. Las 290 filas existentes ya traen valor, así que el cambio no
        //        toca ningún dato.
        DB::statement('ALTER TABLE tb_tes_pago_parcial MODIFY fecha_confirma_pago date NULL');

        // 2) La unicidad del número de eCheq se muda con la columna. Es la garantía real contra
        //    dos abonos con el mismo número — la validación aplicativa solo da el aviso temprano.
        $tieneIndice = collect(DB::select('SHOW INDEX FROM tb_tes_pago_parcial'))
            ->contains(fn($i) => $i->Key_name === 'uq_pp_numero_echeq');

        if (!$tieneIndice) {
            DB::statement('ALTER TABLE tb_tes_pago_parcial ADD UNIQUE KEY uq_pp_numero_echeq (numero_echeq)');
        }

        // 3) Banco emisor -> catálogo de entidades bancarias. int(11) CON SIGNO, como el resto.
        $tieneFk = count(DB::select(
            "SELECT 1 FROM information_schema.KEY_COLUMN_USAGE
             WHERE TABLE_SCHEMA = DATABASE() AND CONSTRAINT_NAME = 'fk_pp_banco_emisor'"
        )) > 0;

        if (!$tieneFk) {
            DB::statement(
                'ALTER TABLE tb_tes_pago_parcial
                 ADD INDEX idx_pp_banco_emisor (id_banco_emisor),
                 ADD CONSTRAINT fk_pp_banco_emisor FOREIGN KEY (id_banco_emisor)
                   REFERENCES tb_tes_entidades_bancarias (id_entidad_bancaria) ON DELETE RESTRICT'
            );
        }

        // 4) Sacar lo que quedó de más en la boleta. `num_cheque` NO se toca: tiene 9 filas con
        //    dato y lo usan pantallas viejas.
        $huboDatos = DB::table('tb_tes_pago')
            ->where(function ($q) {
                $q->whereNotNull('numero_echeq')
                    ->orWhereNotNull('id_estado_instrumento')
                    ->orWhereNotNull('fecha_emision_echeq')
                    ->orWhereNotNull('id_banco_emisor');
            })->count();

        if ($huboDatos > 0) {
            throw new \Exception(
                "Hay {$huboDatos} pago(s) con datos de instrumento cargados en tb_tes_pago. "
                . 'Este cambio asume que están vacías: migrarlos a tb_tes_pago_parcial antes de continuar.'
            );
        }

        if (count(DB::select("SELECT 1 FROM information_schema.KEY_COLUMN_USAGE
             WHERE TABLE_SCHEMA = DATABASE() AND CONSTRAINT_NAME = 'fk_pago_banco_emisor'")) > 0) {
            DB::statement('ALTER TABLE tb_tes_pago DROP FOREIGN KEY fk_pago_banco_emisor');
        }

        foreach (['uq_numero_echeq', 'idx_pago_banco_emisor'] as $indice) {
            if (collect(DB::select('SHOW INDEX FROM tb_tes_pago'))->contains(fn($i) => $i->Key_name === $indice)) {
                DB::statement("ALTER TABLE tb_tes_pago DROP INDEX {$indice}");
            }
        }

        foreach (['id_estado_instrumento', 'numero_echeq', 'fecha_emision_echeq', 'id_banco_emisor'] as $columna) {
            if (Schema::hasColumn('tb_tes_pago', $columna)) {
                DB::statement("ALTER TABLE tb_tes_pago DROP COLUMN {$columna}");
            }
        }
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE tb_tes_pago
            ADD COLUMN id_estado_instrumento int(11) NULL AFTER id_estado_orden_pago,
            ADD COLUMN numero_echeq varchar(50) NULL AFTER num_cheque,
            ADD COLUMN fecha_emision_echeq date NULL AFTER numero_echeq,
            ADD COLUMN id_banco_emisor int(11) NULL AFTER fecha_emision_echeq");
        DB::statement('ALTER TABLE tb_tes_pago ADD UNIQUE KEY uq_numero_echeq (numero_echeq)');

        DB::statement('ALTER TABLE tb_tes_pago_parcial MODIFY fecha_confirma_pago date NOT NULL');
        DB::statement('ALTER TABLE tb_tes_pago_parcial DROP FOREIGN KEY fk_pp_banco_emisor');
        DB::statement('ALTER TABLE tb_tes_pago_parcial DROP INDEX idx_pp_banco_emisor');
        DB::statement('ALTER TABLE tb_tes_pago_parcial DROP INDEX uq_pp_numero_echeq');

        foreach (['id_estado_instrumento', 'numero_echeq', 'fecha_emision_echeq', 'id_banco_emisor',
                  'motivo_rechazo', 'fecha_rechazo'] as $columna) {
            DB::statement("ALTER TABLE tb_tes_pago_parcial DROP COLUMN {$columna}");
        }
    }
};
