<?php

namespace App\Http\Controllers\Tesoreria\Services;

use App\Http\Controllers\Tesoreria\Repository\TesConciliacionBancariaRepository;
use App\Http\Controllers\Tesoreria\Repository\TesCuentasBancariasRepository;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;

class TesConciliacionBancariaController extends Controller
{

    public function getProcesar(
        Request $request,
        TesConciliacionBancariaRepository $conciliacion,
        TesCuentasBancariasRepository $cuenta
    ) {
        try {
            DB::beginTransaction();
            $menssage = "Conciliación bancaria  registrado con éxito.";
            if (is_null($request->id_conciliacion)) {
                $conciliacion->findByNueva($request);
            } else {
                $conciliacion->findByUpdate($request);
                $menssage = "Conciliación bancaria modificada con éxito";
            }

            DB::commit();
            return response()->json(['message' => $menssage]);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'code' => $th->getCode(),
                'message' => $th->getMessage()
            ], 500);
        }
    }

    public function getListar(
        Request $request,
        TesConciliacionBancariaRepository $conciliacion
    ) {
        $data = [];

        if (!is_null($request->cuenta)) {
            $data = $conciliacion->findbyListBetweenAndCuenta($request->desde, $request->hasta, $request->cuenta);
        } else {
            $data = $conciliacion->findbyListBetween($request->desde, $request->hasta);
        }

        return response()->json($data);
    }
}
