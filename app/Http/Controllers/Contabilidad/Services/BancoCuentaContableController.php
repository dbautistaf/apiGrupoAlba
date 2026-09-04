<?php

namespace App\Http\Controllers\Contabilidad\Services;

use App\Http\Controllers\Contabilidad\Repository\BancoCuentaContableRepository;
use App\Models\Contabilidad\BancoCuentasContableEntity;
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
            // Una cuenta bancaria tiene una relacion de BANCO y, si emite eCheq, otra de
            // ECHEQ_DIFERIDO. El duplicado se chequea por (cuenta, tipo), que es lo que
            // impide el indice unico real. (2026-09-04)
            $tipo = $request->tipo ?: BancoCuentasContableEntity::TIPO_BANCO;

            if (!in_array($tipo, [BancoCuentasContableEntity::TIPO_BANCO, BancoCuentasContableEntity::TIPO_ECHEQ_DIFERIDO], true)) {
                return response()->json(['message' => 'El tipo de cuenta contable no es válido.'], 422);
            }

            $etiqueta = $tipo === BancoCuentasContableEntity::TIPO_ECHEQ_DIFERIDO
                ? 'eCheq diferido'
                : 'banco/caja';

            if (!is_null($request->id_banco_cuenta_contable)) {
                if ($repo->findByExisteRelacion($request->id_cuenta_bancaria, $tipo, $request->id_banco_cuenta_contable)) {
                    return response()->json(
                        ['message' => "Esa cuenta bancaria ya tiene una relación de {$etiqueta}."],
                        409
                    );
                }

                $repo->findByUpdate($request, $request->id_banco_cuenta_contable);
                DB::commit();
                return response()->json(["message" => "Registro modifico con éxito."], 200);
            } else {
                if ($repo->findByExisteRelacion($request->id_cuenta_bancaria, $tipo)) {
                    return response()->json(
                        ['message' => "Esa cuenta bancaria ya tiene una relación de {$etiqueta}."],
                        409
                    );
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
