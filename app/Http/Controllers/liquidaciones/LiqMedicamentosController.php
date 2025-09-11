<?php

namespace App\Http\Controllers\liquidaciones;

use App\Http\Controllers\facturacion\repository\FacturasPrestadoresRepository;
use App\Http\Controllers\liquidaciones\repository\LiqDetalleMedicamentosRepository;
use App\Http\Controllers\liquidaciones\repository\LiqMedicamentosRepository;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;

class LiqMedicamentosController extends Controller
{

    public function postProcesar(
        FacturasPrestadoresRepository $repoFactura,
        LiqMedicamentosRepository $repoLiq,
        LiqDetalleMedicamentosRepository $repoDetalle,
        Request $request
    ) {
        DB::beginTransaction();
        try {
            $totalFacturado  = 0;
            $totalAprobado  = 0;
            $totalDebitado  = 0;
            $fechaActual = Carbon::now('America/Argentina/Buenos_Aires');

            if (is_null($request->id_liquidacion)) {
                $liquidacion = $repoLiq->save($request);

                foreach ($request->detalle as $key) {
                    $repoDetalle->save($key, $liquidacion->id_liquidacion);
                    $totalFacturado  += $key['importe_facturado'];
                }

                $repoFactura->findByUpdateEstadoAndmontoFacturadoAndmontoAprobadoAndmontoDebitadoAndIdfacturaAndfechaLiquida(
                    '1',
                    $totalFacturado,
                    $totalAprobado,
                    $totalDebitado,
                    $fechaActual,
                    $liquidacion->id_factura,
                );

                DB::commit();
                return response()->json(["message" => "Datos procesados correctamente"]);
            } else {
                $liquidacion = $repoLiq->saveId($request->id_liquidacion, $request);

                foreach ($request->detalle as $key) {
                    $repoDetalle->saveId($key['id_detalle'], $key);
                    $totalFacturado  += $key['importe_facturado'];
                }

                $repoFactura->findByUpdateEstadoAndmontoFacturadoAndmontoAprobadoAndmontoDebitadoAndIdfactura(
                    '1',
                    $totalFacturado,
                    $totalAprobado,
                    $totalDebitado,
                    $liquidacion->id_factura,
                );
                DB::commit();
                return response()->json(["message" => "Datos actualizado correctamente"]);
            }
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'message' => $th->getMessage()
            ], 500);
        }
    }

    public function getObtenerdatosEditarMedicamnetos(LiqMedicamentosRepository $repoLiq, Request $request)
    {
        $datos = $repoLiq->findByMedicamentosCabeceraId($request->id);
        $detalle = $repoLiq->findByMedicamentosDetalleId($request->id);

        return response()->json(["datos" => $datos[0] ?? null, "detalle" => $detalle]);
    }
}
