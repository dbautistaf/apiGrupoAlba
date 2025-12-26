<?php
namespace App\Http\Controllers\Contabilidad\Services;

use App\Http\Controllers\Contabilidad\Repository\PeriodosContablesRepository;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;

class PeriodosContablesService extends Controller
{


    public function getListar(Request $request, PeriodosContablesRepository $periodosContablesRepository)
    {
        return response()->json($periodosContablesRepository->findByList($request));
    }
    public function getListarPeriodosAnuales(Request $request, PeriodosContablesRepository $periodosContablesRepository)
    {
        return response()->json($periodosContablesRepository->findByListAnual($request));
    }

    public function getProcesar(Request $request, PeriodosContablesRepository $periodosContablesRepository)
    {
        DB::beginTransaction();
        try {
            if (is_null($request->id_periodo_contable)) {
                if ($periodosContablesRepository->findByExistsAnio($request->anio_periodo)) {
                    DB::rollBack();
                    return response()->json(["message" => "El Periodo contable <b>{$request->anio_periodo}</b> ya éxiste."], 409);
                }
                $periodosContablesRepository->findByCreate($request);
                DB::commit();
                return response()->json(["message" => "Periodo contable aperturado correctamente."], 200);
            } else {
                $periodosContablesRepository->findByUpdate($request, $request->id_periodo_contable);
                DB::commit();
                return response()->json(["message" => "Periodo contable actualizado correctamente."], 200);
            }
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'code' => $th->getCode(),
                'message' => $th->getMessage()
            ], 500);
        }
    }

    public function toggleActivo($id_periodo_contable, PeriodosContablesRepository $periodosContablesRepository)
    {
        DB::beginTransaction();
        try {
            $periodosContablesRepository->toggleActivo($id_periodo_contable);
            DB::commit();
            return response()->json(["message" => "Estado 'activo' actualizado correctamente."], 200);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'code' => $th->getCode(),
                'message' => $th->getMessage()
            ], 500);
        }
    }

    public function toggleVigente($id_periodo_contable, PeriodosContablesRepository $periodosContablesRepository)
    {
        DB::beginTransaction();
        try {
            $periodosContablesRepository->toggleVigente($id_periodo_contable);
            DB::commit();
            return response()->json(["message" => "Estado 'vigente' actualizado correctamente."], 200);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'code' => $th->getCode(),
                'message' => $th->getMessage()
            ], 500);
        }
    }
}
