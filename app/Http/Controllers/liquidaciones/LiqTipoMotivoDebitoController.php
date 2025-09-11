<?php

namespace App\Http\Controllers\liquidaciones;
use App\Http\Controllers\liquidaciones\repository\LiqTipoMotivoDebitoRepository;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class LiqTipoMotivoDebitoController extends Controller
{

    public function getListarTipoMotivoDebito(LiqTipoMotivoDebitoRepository $repo, Request $request)
    {
        return response()->json($repo->findByListDescripcion($request->search, '1', 50));
    }
}
