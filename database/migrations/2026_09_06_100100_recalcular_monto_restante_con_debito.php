<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Recalcula `tb_tes_pago_parcial.monto_restante` descontando el débito de liquidación.
 *
 * ═══ Qué estaba mal ═══
 *
 * `monto_restante` se guardaba como `monto_orden_pago − lo emitido`, y `monto_orden_pago` arrastra
 * el **bruto** de la factura. Al pagar el neto correcto quedaba un "restante" exactamente igual al
 * débito, sobre una orden que en realidad ya estaba saldada.
 *
 * Es el número que el modal de **Confirmar Pago** muestra arriba como "Monto Restante Actual".
 * Reportado el 2026-09-06: decía **$576,00** sobre una orden de $7.866 con $576 de débito, ya
 * cubierta con $7.290.
 *
 * El cálculo nuevo (en `emitirPagoDeFecha`) usa el monto pagable. Esta migración pone al día los
 * abonos que ya estaban guardados: 57 en Alba y 30 en OSV al momento de escribirla.
 *
 * ═══ Cómo ═══
 *
 * `monto_restante` es acumulativo: el de cada abono depende de lo emitido *hasta ese abono
 * inclusive*. Se recalcula con una window function ordenando por `id_pago_parcial`, que es el
 * orden en que se fueron emitiendo.
 *
 * Los abonos RECHAZADOS (5) y ANULADOS (6) no suman al acumulado: esa plata no salió o volvió,
 * mismo criterio que usa el freno de sobrepago.
 *
 * Las órdenes SIN facturas imputadas (los anticipos) no entran: no tienen débito que descontar y
 * su restante contra `monto_orden_pago` ya era correcto.
 */
return new class extends Migration
{
    public function up(): void
    {
        DB::statement('
            UPDATE tb_tes_pago_parcial pp
            JOIN (
                SELECT
                    pp2.id_pago_parcial,
                    GREATEST(0, pag.pagable - SUM(
                        CASE WHEN pp2.id_estado_instrumento IN (5, 6) THEN 0
                             ELSE COALESCE(pp2.monto_pago, 0) END
                    ) OVER (
                        PARTITION BY p2.id_orden_pago
                        ORDER BY pp2.id_pago_parcial
                        ROWS BETWEEN UNBOUNDED PRECEDING AND CURRENT ROW
                    )) AS restante
                FROM tb_tes_pago_parcial pp2
                JOIN tb_tes_pago p2 ON p2.id_pago = pp2.id_pago
                JOIN (
                    SELECT
                        pf.id_orden_pago,
                        SUM(LEAST(
                            pf.monto_aplicado,
                            GREATEST(0, f.total_neto - COALESCE(f.total_debitado_liquidacion, 0))
                        )) AS pagable
                    FROM tb_tes_opa_factura pf
                    JOIN tb_facturacion_datos f ON f.id_factura = pf.id_factura
                    GROUP BY pf.id_orden_pago
                ) pag ON pag.id_orden_pago = p2.id_orden_pago
            ) calc ON calc.id_pago_parcial = pp.id_pago_parcial
            SET pp.monto_restante = calc.restante
        ');
    }

    public function down(): void
    {
        // No se revierte: `monto_restante` es un valor derivado, y el valor anterior era el que
        // estaba mal. Si hiciera falta, se recalcula contra `monto_orden_pago` con la misma
        // consulta cambiando `pag.pagable` por el monto de la orden.
    }
};
