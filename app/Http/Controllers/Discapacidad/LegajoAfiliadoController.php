<?php

namespace App\Http\Controllers\Discapacidad;

use App\Http\Controllers\Discapacidad\Repository\DiscapacidadLegajoRepository;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;

class LegajoAfiliadoController extends Controller
{

    public function getProcesar(Request $request, DiscapacidadLegajoRepository $repo)
    {
        DB::beginTransaction();

        try {
            if (!is_null($request->id_legajo)) {
                $repo->findbyUpdate($request, null);
            } else {
                $repo->findByCrearLegajo($request, null);
            }
            DB::commit();
            return response()->json(["message" => "Registro procesado correctamente"]);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'message' => $th->getMessage()
            ], 500);
        }
    }

    public function getListarLegajo(Request $request, DiscapacidadLegajoRepository $repo)
    {
        return response()->json($repo->findByListar($request));
    }

    public function getObtenerLegajoId(Request $request, DiscapacidadLegajoRepository $repo)
    {
        return response()->json($repo->findById($request->id));
    }

    public function getEliminarLegajoId(Request $request, DiscapacidadLegajoRepository $repo)
    {
        $repo->findByDeleteId($request->id);
        return response()->json(['message' => 'Registro eliminado correctamente']);
    }
}
