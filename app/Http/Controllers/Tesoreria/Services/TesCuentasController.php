<?php

namespace App\Http\Controllers\Tesoreria\Services;

use App\Http\Controllers\Tesoreria\Repository\TesCuentasBancariasRepository;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;

class TesCuentasController extends Controller
{

    public function getProcesarCuenta(Request $request, TesCuentasBancariasRepository $repoCuenta)
    {
        try {
            DB::beginTransaction();
            $menssage = "La cuenta <b>{$request->nombre_cuenta}</b>, se registro con éxito.";
            if (is_null($request->id_cuenta_bancaria)) {
                $repoCuenta->findByCreate($request);
            } else {
                $repoCuenta->findByUpdate($request);
                $menssage = "La cuenta <b>{$request->nombre_cuenta}</b>, se actualizo con éxito.";
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


    public function getValidarSaldoCuenta(Request $request, TesCuentasBancariasRepository $repoCuenta)
    {
        if (!$repoCuenta->findByVerificarSaldoCuenta($request->id_cuenta_bancaria, $request->monto)) {
            DB::rollBack();
            return response()->json(['message' => 'No hemos podido procesar tu solicitud de pago porque la cuenta bancaria seleccionada no tiene fondos suficientes. Por favor, revisa tu saldo e inténtalo otra vez.'], 409);
        }

        return response()->json(['saldo' => true]);
    }
}
