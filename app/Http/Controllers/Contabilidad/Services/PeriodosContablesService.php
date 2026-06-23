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
                // Validar si es período anual o mensual
                if (isset($request->mes)) {
                    // Validación para período mensual: tipo, año, mes y razón social
                    if ($periodosContablesRepository->findByExistsPeriodoMensual($request->anio_periodo, $request->mes, $request->id_razon)) {
                        DB::rollBack();
                        return response()->json(["message" => "El Periodo contable mensual <b>{$request->anio_periodo}-{$request->mes}</b> ya existe para esta razón social."], 409);
                    }
                } else {
                    // Validación para período anual: tipo, año y razón social
                    if ($periodosContablesRepository->findByExistsPeriodoAnual($request->anio_periodo, $request->id_razon)) {
                        DB::rollBack();
                        return response()->json(["message" => "El Periodo contable anual <b>{$request->anio_periodo}</b> ya existe para esta razón social."], 409);
                    }
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
            $idRazon = request()->id_razon ?? null;
            $periodosContablesRepository->toggleActivo($id_periodo_contable, $idRazon);
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
            $idRazon = request()->id_razon ?? null;
            $periodosContablesRepository->toggleVigente($id_periodo_contable, $idRazon);
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
