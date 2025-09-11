<?php

namespace App\Http\Controllers\Diabetes\Services;

use App\Http\Controllers\afiliados\repository\PadronAfiliadoRepository;
use App\Http\Controllers\Diabetes\Repository\DiabetesRepository;
use App\Models\Diabetes\DiabetesEntity;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;

class DiabetesController extends Controller
{
    public function getListarDiabetes(Request $request, DiabetesRepository $repository)
    {
        $data = $repository->findByListarSolicitudes($request);
        return response()->json($data);
    }

    public function getProcesar(Request $request, DiabetesRepository $repository, PadronAfiliadoRepository $padronAfiliado)
    {
        DB::beginTransaction();
        try {
            $message = "Registro procesado con éxito";
            if (!is_null($request->id_diabetes)) {

                if (strlen($request->dni_afiliado) < 8) {
                    $afiliado = $padronAfiliado->findByIdAfiliado($request->dni_afiliado);
                    $request->merge(['dni_afiliado' => $afiliado->dni]);
                    $request->merge(['id_padron' => $afiliado->id]);
                }

                $diabetes = $repository->findByUpdate($request);

                foreach ($request->detalle as $value) {
                    if (is_null($value['id_diabetes_detalle'])) {
                        $repository->findByCrearDetalle($value, $diabetes->id_diabetes);
                    } else {
                        $repository->findByUpdateItemDetalle($value, $diabetes->id_diabetes);
                    }
                }
                $message = "Registro modificado con éxito";
            } else {
                $afiliado = $padronAfiliado->findByIdAfiliado($request->dni_afiliado);
                $request->merge(['dni_afiliado' => $afiliado->dni]);
                $request->merge(['id_padron' => $afiliado->id]);

                $diabetes = $repository->findByCrear($request);

                foreach ($request->detalle as $value) {
                    $repository->findByCrearDetalle($value, $diabetes->id_diabetes);
                }
            }

            DB::commit();
            return response()->json(["message" => $message]);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'code' => $th->getCode(),
                'message' => $th->getMessage()
            ], 500);
        }
    }

    public function getAnularSolicitud(Request $request, DiabetesRepository $repository)
    {
        $repository->findByAnularSolicitud($request->id);
        return response()->json(['message' => 'Registro anulado correctamente']);
    }

    public function getEliminarItemDetalle(Request $request, DiabetesRepository $repository)
    {
        $repository->findByEliminarItem($request->id);
        return response()->json(['message' => 'Registro eliminado correctamente']);
    }

    public function getListar(Request $request)
    {
        $data = null;
        $sql = DiabetesEntity::with(['afiliado', 'tipoDiabetes']);
        if (!is_null($request->searchs)) {
            $sql->whereHas('afiliado', function ($subQuery) use ($request) {
                $subQuery->whereRaw("CONVERT(dni USING utf8mb4) COLLATE utf8mb4_unicode_ci LIKE ?", ["%{$request->searchs}%"])
                    ->orWhereRaw("CONVERT(apellidos USING utf8mb4) COLLATE utf8mb4_unicode_ci LIKE ?", ["%{$request->searchs}%"]);
            });
        }

        if (!is_null($request->desde) && !is_null($request->hasta)) {
            $sql->whereBetween('fecha_baja', [$request->desde, $request->hasta]);
        }

        if (!is_null($request->id_tipo_diabetes)) {
            $sql->where('id_tipo_diabetes', $request->id_tipo_diabetes);
        }

        $data = $sql->get();

        return response()->json($data);
    }

    public function getBuscarId(Request $request)
    {
        return response()->json(DiabetesEntity::with(['afiliado'])->find($request->id));
    }
}
