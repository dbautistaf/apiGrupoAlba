<?php

namespace   App\Http\Controllers\Protesis\Services;

use App\Http\Controllers\Protesis\Repository\TipoAutorizacionRepository;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class TipoAutorizacionController extends Controller
{

    public function getListarVigentes(TipoAutorizacionRepository $repo, Request $request)
    {
        return response()->json($repo->findByListVigente($request->estado), 200);
    }
}
