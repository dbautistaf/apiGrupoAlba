<?php

namespace App\Http\Controllers\procesos;

use App\Models\AuditarPrestacionesPracticaLaboratorioEntity;
use App\Models\CostoPracticaLaboratorioEntity;
use App\Models\DetallePrestacionesPracticaLaboratorioEntity;
use App\Models\PrestacionesPracticaLaboratorioEntity;
use App\Models\TipoPracticasLaboratorioEntity;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ProcesosPrestacionesPracticaLaboratorioController extends Controller
{

    public function postObtenerCostoPractica(Request $request)
    {
        try {
            $dtCostoPractica = null;
            // #BUSCAR LA PRACTICA PARA OBTENER EL CODIGO DE VALOR
            $tipoPracticaLaboratorio = TipoPracticasLaboratorioEntity::find($request->cod_tipo_practica);
            // #BUSCAR EL AFILIADO PARA OBTNER SU TAG Y VER QUE COSTO LE CORRESPONDE Y TAMBIEN EL TIPO DE PLAN QUE TIENE

            // #SI TIENE ALGUN TAG SU VALOR DE PAGO ES 0

            // #BUSCALOS EL VALOR DE COSTO DE LA PRACTICA SEGUN EL PLAN Y TAG
            $costoPracticaLaboratorio = CostoPracticaLaboratorioEntity::where('afiliado_procede', '1')
                ->where('practica_valor', $tipoPracticaLaboratorio->practica_valor)
                ->first();

            if (empty($costoPracticaLaboratorio)) {
                $dtCostoPractica = [
                    "costo_practica" => "0.00",
                    "practica_valor" => $tipoPracticaLaboratorio->practica_valor
                ];
            } else {
                $dtCostoPractica = [
                    "costo_practica" => $costoPracticaLaboratorio->costo_practica,
                    "practica_valor" => $tipoPracticaLaboratorio->practica_valor
                ];
            }

            return response()->json($dtCostoPractica, 200);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => $th->getMessage()
            ], 500);
        }
    }
}
