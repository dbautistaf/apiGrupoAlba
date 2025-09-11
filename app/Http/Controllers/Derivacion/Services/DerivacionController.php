<?php

namespace App\Http\Controllers\Derivacion\Services;

use App\Http\Controllers\Derivacion\Repository\DerivacionRepository;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;

class DerivacionController extends Controller
{

    public function getProcesar(DerivacionRepository $repo, Request $request)
    {

        DB::beginTransaction();
        try {

            if (!is_null($request->num_internacion)) {
                if (!$repo->findByExistsInternacion($request->num_internacion)) {
                    DB::rollBack();
                    return response()->json([
                        'message' => "El numero de internación <b>$request->num_internacion</b> no éxiste."
                    ], 409);
                }
            }

            if (is_null($request->id_derivacion)) {
                $medico = $repo->findBySaveDatosMedicos($request);
                $repo->findBySaveDerivacion($request, $medico);
                DB::commit();
                return response()->json(["message" => "Solicitud registrada correctamente."], 200);
            } else {
                $medico = $repo->findByUpdateDatosMedicos($request);
                $repo->findByUpdateDerivacion($request, $medico);
                DB::commit();
                return response()->json(["message" => "Solicitud actualizada correctamente."], 200);
            }
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'code' => $th->getCode(),
                'message' => $th->getMessage()
            ], 500);
        }
    }

    public function getBuscarId(DerivacionRepository $repo, Request $request)
    {
        return response()->json($repo->findById($request->id));
    }

    public function getActulizarEstado(DerivacionRepository $repo, Request $request)
    {
        $message = "INTENTE MAS TARDE";
        $code = 409;
        if ($request->estado === 1) {
            $repo->findByUpdateEstado($request->id, $request->estado);
            $message = "Solicitud anulada correctamente";
            $code = 200;
        }
        return response()->json(["message" => $message], $code);
    }

    public function getAutorizarDerivacion(DerivacionRepository $repo, Request $request)
    {
        DB::beginTransaction();
        try {
            $repo->findByAutorizarDerivacion($request);
            $repo->findByUpdateEstado($request->id_derivacion, $request->id_tipo_estado);
            DB::commit();
            return response()->json(["message" => "Auditoria registrada correctamente"], 200);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'code' => $th->getCode(),
                'message' => $th->getMessage()
            ], 500);
        }
    }
}
