<?php
namespace App\Http\Controllers\Tesoreria\Services;

use App\Http\Controllers\Tesoreria\Repository\TesCuentasBancariasRepository;
use App\Http\Controllers\Tesoreria\Repository\TesCuentasBloqueadasRepository;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TesCuentasBloqueoController extends Controller
{

    public function getBloquear(Request $request, TesCuentasBloqueadasRepository $repo, TesCuentasBancariasRepository $cuenta)
    {
        try {

            DB::beginTransaction();
            $repo->findBySave($request);
            $cuentaDB = $cuenta->findByUpdateEstado($request->id_cuenta_bancaria, $request->estado);
            DB::commit();
            return response()->json(["message" => "Se procedio con el ".($request->estado == '0' ? 'BLOQUEO' : 'DESBLOQUEO')." de la cuenta <b>{$cuentaDB->nombre_cuenta}</b> de forma éxitosa"]);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'code' => $th->getCode(),
                'message' => $th->getMessage()
            ], 500);
        }

    }

}
