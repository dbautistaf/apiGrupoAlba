<?php

namespace   App\Http\Controllers\Protesis\Services;

use App\Http\Controllers\Protesis\Repository\CatalogoProtesisRepository;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;

class CatalogoController extends Controller
{

    public function getListarCondicionProtesis(CatalogoProtesisRepository $repo)
    {
        return response()->json($repo->findByTipoCondicionProtesis(), 200);
    }

    public function getListarEstadoSolicitud(CatalogoProtesisRepository $repo)
    {
        return response()->json($repo->findByEstadoSolicitudProtesis(), 200);
    }

    public function getListarOrigenMaterialProtesis(CatalogoProtesisRepository $repo)
    {
        return response()->json($repo->findByOrigenMaterialProtesis(), 200);
    }

    public function getListarProgramaEspecialProtesis(CatalogoProtesisRepository $repo)
    {
        return response()->json($repo->findByProgramaEspecialProtesis(), 200);
    }

    public function getListarTipoCobertura(CatalogoProtesisRepository $repo)
    {
        return response()->json($repo->findByTipoCoberturaProtesis(), 200);
    }

    public function getListarTipoDiagnostico(CatalogoProtesisRepository $repo, Request $request)
    {
        $data = [];
        if (!is_null($request->identificador) && is_null($request->descripcion)) {
            $data = $repo->findByListTipoDiagnosticoCodigoLikeLimit($request->identificador, 20);
        } else if (is_null($request->identificador) && !is_null($request->descripcion)) {
            $data = $repo->findByListTipoDiagnosticoDescripcionLikeLimit($request->descripcion, 20);
        } else {
            $data = $repo->findByListTipoDiagnosticoLimit(50);
        }
        return response()->json($data, 200);
    }

    public function getTipoDiagnosticoId(CatalogoProtesisRepository $repo, Request $request)
    {
        $data = $repo->findByTipoDiagnosticoId($request->id_diagnostico);
        return response()->json($data, 200);
    }

    public function getsaveTipoDiagnostico(CatalogoProtesisRepository $repo,   Request $request)
    {
        DB::beginTransaction();
        try {
            $repo->findBySaveTipoDiagnostico($request);
            DB::commit();
            return response()->json(['message' => 'Tipo Diagnostico guardado correctamente']);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'code' => $th->getCode(),
                'message' => $th->getMessage()
            ], 500);
        }
    }
}
