<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CrearDeudaEmpresas extends Command
{
    protected $signature = 'insert:monthly-deuda';
    protected $description = 'Inserta datos mensuales en la tabla tb_fisca_deudas_aportes_empresa';

    public function handle()
    {
        $periodo = now()->subMonth()->format('ym'); // ejemplo: 2507

        DB::statement("
            INSERT INTO tb_fisca_deudas_aportes_empresa (
                anio, mes, importe_sueldo, aporte, contribucion,
                contribucion_extraordinaria, fecha_recalculo, fecha_vencimiento,
                id_empresa, intereses, monto_deuda, monto_estudio_juridico,
                monto_gestion_morosidad, tipo_deuda, estado
            )
            SELECT
                CONCAT('20', SUBSTRING(ddjj.periodo, 1, 2)) AS anio,
                SUBSTRING(ddjj.periodo, 3, 2) AS mes,
                SUM(ddjj.remimpo) AS importe_sueldo,
                ROUND(SUM(ddjj.remimpo) * 0.03, 2) AS aporte,
                ROUND(SUM(ddjj.remimpo) * 0.06, 2) AS contribucion,
                NULL AS contribucion_extraordinaria,
                NOW() AS fecha_recalculo,
                DATE_ADD(NOW(), INTERVAL 30 DAY) AS fecha_vencimiento,
                emp.id_empresa,
                NULL AS intereses,
                ROUND(SUM(ddjj.remimpo) * 0.09, 2) AS monto_deuda,
                NULL AS monto_estudio_juridico,
                NULL AS monto_gestion_morosidad,
                'APORTE' AS tipo_deuda,
                'Vigente' AS estado
            FROM tb_declaraciones_juradas ddjj
            LEFT JOIN tb_transferencias trf
                ON ddjj.cuil = trf.cuitcont
                AND ddjj.periodo = trf.periodo
            INNER JOIN tb_empresa emp
                ON emp.cuit = ddjj.cuit
            WHERE trf.id_transferencia IS NULL
              AND ddjj.periodo = :periodo
              AND NOT EXISTS (
                  SELECT 1 
                  FROM tb_fisca_deudas_aportes_empresa f
                  WHERE f.id_empresa = emp.id_empresa
                    AND f.anio = CONCAT('20', SUBSTRING(ddjj.periodo, 1, 2))
                    AND f.mes  = SUBSTRING(ddjj.periodo, 3, 2)
                    AND f.tipo_deuda = 'APORTE'
              )
            GROUP BY emp.id_empresa, anio, mes
        ", ['periodo' => $periodo]);

        $this->info("Datos insertados correctamente para período $periodo ✅");
        return 0;
    }
}
