<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Vincula cada ABONO con la FECHA PROBABLE que lo planificó.
 *
 * Confirmar la orden de pago define el **cronograma**: en qué fechas se va a pagar y en cuántas
 * cuotas. Nada más. El monto y la forma de pago (eCheq o transferencia) se deciden recién al
 * emitir cada pago, que es cuando Tesorería sabe con qué lo va a pagar.
 *
 * Por eso el abono NO se crea al confirmar la orden — `monto_pago`, `monto_opa`, `monto_restante`
 * e `id_forma_pago` son todas NOT NULL, así que un abono sin monto ni forma no puede existir, y
 * poner ceros de relleno sería inventar datos.
 *
 * El circuito queda:
 *
 *     Confirmar OP  ->  N fechas probables (el plan)
 *     Emitir cada pago  ->  1 abono por fecha, con monto, forma y (si es eCheq) número
 *
 * Esta columna es la que permite saber qué fechas ya tienen su pago emitido y cuáles siguen
 * esperando. Sin ella habría que adivinar por orden o por cantidad.
 *
 * `int(11)` CON SIGNO, como el resto de las tablas viejas.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('tb_tes_pago_parcial', 'id_fecha_probable')) {
            DB::statement('ALTER TABLE tb_tes_pago_parcial ADD COLUMN id_fecha_probable int(11) NULL AFTER id_pago');
            DB::statement('ALTER TABLE tb_tes_pago_parcial ADD INDEX idx_pp_fecha_probable (id_fecha_probable)');

            // La FK se crea solo si no hay abonos viejos apuntando a nada: los 290 existentes
            // son anteriores a esta columna y quedan en NULL, que la FK admite.
            DB::statement(
                'ALTER TABLE tb_tes_pago_parcial
                 ADD CONSTRAINT fk_pp_fecha_probable FOREIGN KEY (id_fecha_probable)
                   REFERENCES tb_tes_fecha_probable_pago (id_fecha_probable) ON DELETE SET NULL'
            );
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('tb_tes_pago_parcial', 'id_fecha_probable')) {
            DB::statement('ALTER TABLE tb_tes_pago_parcial DROP FOREIGN KEY fk_pp_fecha_probable');
            DB::statement('ALTER TABLE tb_tes_pago_parcial DROP INDEX idx_pp_fecha_probable');
            DB::statement('ALTER TABLE tb_tes_pago_parcial DROP COLUMN id_fecha_probable');
        }
    }
};
