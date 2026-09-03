<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * BUG DE NUMERACIÓN: `LPAD(x, n, '0')` no solo rellena — TRUNCA cuando el número tiene más de
 * n dígitos. `LPAD(14428, 4, '0')` devuelve '1442', no '14428'.
 *
 * Consecuencia: cuando la secuencia `sec_correlativos` pasó los 9.999, cada 10 valores
 * consecutivos empezaron a colapsar en el mismo número. Verificado en OSV el 2026-09-03:
 *
 *   tg_asignar_numero_opa          (LPAD 4) → 1.430 OPAs con número repetido sobre 4.643
 *   tg_asignar_numero_protesis     (LPAD 5) → 6 autorizaciones repetidas sobre 85
 *   tg_asignar_numero_liquidacion  (LPAD 8) → todavía sano, mismo defecto latente
 *   tg_asignar_numero_internacion  (LPAD 5) → todavía sano, mismo defecto latente
 *
 * Peor aún, el número nuevo choca con uno viejo legítimo: la secuencia 10000 produce 'OPA-1000',
 * el mismo que produjo la secuencia 1000 en su momento. Hay colisiones separadas por 295 días.
 *
 * ARREGLO: rellenar sólo mientras el número entre en el ancho; nunca truncar. Por debajo del
 * umbral el resultado es IDÉNTICO al actual, así que los números ya emitidos no cambian de forma
 * y no se toca nada del histórico.
 *
 * Esta migración NO renumera las filas existentes: esos números están en documentos ya enviados
 * a proveedores y prestadores, y decidir qué hacer con ellos es una decisión del negocio.
 */
return new class extends Migration
{
    /** [trigger, tabla, columna, prefijo, ancho de padding] */
    private array $triggers = [
        ['tg_asignar_numero_opa',                    'tb_tes_orden_pago',    'num_orden_pago',   'OPA-', 4],
        ['tg_asignar_numero_autorizacion_protesis',  'tb_protesis',          'num_autorizacion', '',     5],
        ['tg_asignar_numero_internacion',            'tb_internaciones',     'num_internacion',  '',     5],
        ['tg_asignar_numero_liquidacion',            'tb_facturacion_datos', 'num_liquidacion',  '',     8],
    ];

    public function up(): void
    {
        // El máximo de la secuencia es 9.999.999 (7 dígitos). La columna tiene que poder alojar
        // el prefijo más 7 dígitos, o el INSERT va a fallar cuando la secuencia crezca.
        DB::statement('ALTER TABLE tb_tes_orden_pago MODIFY num_orden_pago varchar(20) NULL');

        foreach ($this->triggers as [$trigger, $tabla, $columna, $prefijo, $ancho]) {
            $umbral = (int) str_repeat('9', $ancho) + 1;   // 10^ancho
            $valor  = "IF(LET_SECUENCIAL < {$umbral}, LPAD(LET_SECUENCIAL, {$ancho}, '0'), LET_SECUENCIAL)";
            $expr   = $prefijo === ''
                ? $valor
                : "CONCAT('{$prefijo}', {$valor})";

            DB::unprepared("DROP TRIGGER IF EXISTS {$trigger}");
            DB::unprepared("
                CREATE TRIGGER {$trigger} BEFORE INSERT ON {$tabla}
                FOR EACH ROW
                BEGIN
                    DECLARE LET_SECUENCIAL INT DEFAULT 0;
                    SET LET_SECUENCIAL = NEXTVAL(sec_correlativos);
                    SET NEW.{$columna} = {$expr};
                END
            ");
        }
    }

    public function down(): void
    {
        // Se restauran los triggers originales, con el truncamiento incluido.
        foreach ($this->triggers as [$trigger, $tabla, $columna, $prefijo, $ancho]) {
            $valor = "LPAD(LET_SECUENCIAL, {$ancho}, '0')";
            $expr  = $prefijo === '' ? $valor : "CONCAT('{$prefijo}', {$valor})";

            DB::unprepared("DROP TRIGGER IF EXISTS {$trigger}");
            DB::unprepared("
                CREATE TRIGGER {$trigger} BEFORE INSERT ON {$tabla}
                FOR EACH ROW
                BEGIN
                    DECLARE LET_SECUENCIAL INT DEFAULT 0;
                    SET LET_SECUENCIAL = NEXTVAL(sec_correlativos);
                    SET NEW.{$columna} = {$expr};
                END
            ");
        }

        DB::statement('ALTER TABLE tb_tes_orden_pago MODIFY num_orden_pago varchar(10) NULL');
    }
};
