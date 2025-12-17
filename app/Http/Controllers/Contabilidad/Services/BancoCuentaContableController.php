<?php

namespace App\Http\Controllers\Contabilidad\Services;

use App\Http\Controllers\Contabilidad\Repository\BancoCuentaContableRepository;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;

class BancoCuentaContableController extends Controller
{

    public function getListar(Request $request, BancoCuentaContableRepository $repo)
    {
        $data = $repo->findByListar();

        return response()->json($data);
    }

    public function getProcesar(Request $request, BancoCuentaContableRepository $repo)
    {
        try {
            DB::beginTransaction();
            if (!is_null($request->id_banco_cuenta_contable)) {
                $repo->findByUpdate($request, $request->id_banco_cuenta_contable);
                DB::commit();
                return response()->json(["message" => "Registro modifico con éxito."], 200);
            } else {
                if ($repo->findByExisteRelacion($request->id_cuenta_bancaria, $request->id_detalle_plan)) {
                    return response()->json(['message' => 'Ya éxiste una relacion con la cuenta y el banco para el periodo vigente.'], 409);
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
}
