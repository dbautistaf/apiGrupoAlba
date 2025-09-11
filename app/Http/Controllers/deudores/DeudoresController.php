<?php

namespace App\Http\Controllers\deudores;

use App\Models\afiliado\AfiliadoPadronEntity;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DeudoresController extends Controller
{
    public function getLikeDeudores(Request $request)
    {
        $query =  "WITH AportesUltimos3Meses AS (
            SELECT
                cuitapo AS cuil_afiliado,
                COUNT(*) AS total_aportes
            FROM tb_transferencias
            WHERE fecha_proceso BETWEEN DATE_SUB(CURDATE(), INTERVAL 3 MONTH) AND CURDATE()
            GROUP BY cuitapo
             )

            SELECT
            p.cuil_tit AS cuil_afiliado,
            MAX(p.nombre) AS nombre_afiliado,
            MAX(p.apellidos) AS apellidos_afiliado,
            t.periodo_tranf AS periodo,
            MAX(d.remimpo) AS declaracion_jurada,
            MAX(t.importe) AS aporte,
            MAX(d.fecpresent) AS fecha_presentacion,
            MAX(t.fecha_proceso) AS fecha_pago,
            CASE
                WHEN MAX(d.remimpo) IS NOT NULL AND COALESCE(MAX(a.total_aportes), 0) >= 3 THEN 'PAGA OK'
                WHEN MAX(d.remimpo) IS NOT NULL AND COALESCE(MAX(a.total_aportes), 0) < 3 THEN 'PAGO PARCIAL'
                ELSE 'DEUDOR'
                END AS estado
                FROM tb_padron p
            LEFT JOIN tb_transferencias t ON p.cuil_tit = t.cuitapo
            LEFT JOIN tb_declaraciones_juradas d ON p.cuil_tit = d.cuil
            LEFT JOIN AportesUltimos3Meses a ON p.cuil_tit = a.cuil_afiliado

            ";

        $params = [];

        if (!is_null($request->desde) && !is_null($request->hasta)) {

            $pDesdeArray = explode("-", $request->desde);
            $params[] = $pDesdeArray[1] . "/" . $pDesdeArray[0];

            $pHastaArray = explode("-", $request->hasta);
            $periodoHasta = $pHastaArray[1] . "/" . $pHastaArray[0];
            $query .= " WHERE t.periodo_tranf >= ? ";
        }

        //Log::info($params);

        if (!is_null($request->cuil_afiliado)) {
            $query .= " AND  p.cuil_tit LIKE ?";
            $params[] = $request->cuil_afiliado . '%';
        }

        $query .= " GROUP BY p.cuil_tit, t.periodo_tranf ORDER BY MAX(p.nombre);";

        $results = DB::select($query, $params);

        // Retornar los resultados como respuesta JSON
        return response()->json($results);
    }
}
