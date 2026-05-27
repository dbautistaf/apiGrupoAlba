<?php

namespace App\Http\Controllers\Contabilidad\Services;

use App\Http\Controllers\Contabilidad\Repository\TipoPrestadorPlanesCuentaRepository;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;

class TipoPrestadorPlanesCuentaController extends Controller
{

    public function getListar(Request $request, TipoPrestadorPlanesCuentaRepository $repo)
    {
        $data = $repo->findByListar();

        return response()->json($data);
    }

    public function getProcesar(Request $request, TipoPrestadorPlanesCuentaRepository $repo)
    {
        try {
            DB::beginTransaction();
            if (!is_null($request->cod_tipo_prestador_cuenta_contable)) {
                $repo->findByUpdate($request, $request->cod_tipo_prestador_cuenta_contable);
                DB::commit();
                return response()->json(["message" => "Registro modificado con éxito."], 200);
            } else {
                if ($repo->findByExisteRelacion($request->cod_tipo_prestador, $request->id_detalle_plan)) {
                    return response()->json(['message' => 'Ya éxiste una relacion con la cuenta y el tipo de prestador para el periodo vigente.'], 409);
                }

                $repo->findByCrear($request);
                DB::commit();
                return response()->json(["message" => "Registro procesado con éxito."], 200);
            }
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'code' => $th->getCode(),
                'message' => $th->getMessage()
            ], 500);
        }
    }

    public function getPlanCuentaPorTipoPrestador($codTipoPrestador, TipoPrestadorPlanesCuentaRepository $repo)
    {
        try {
            $planCuenta = $repo->findByPlanCuentaPorTipoPrestador($codTipoPrestador);

            if (!$planCuenta) {
                return response()->json([
                    'message' => 'No se encontró plan de cuenta para el tipo de prestador especificado'
                ], 404);
            }

            return response()->json($planCuenta, 200);
        } catch (\Throwable $th) {
            return response()->json([
                'code' => $th->getCode(),
                'message' => $th->getMessage()
            ], 500);
        }
    }
}
