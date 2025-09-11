<?php

namespace App\Http\Controllers\convenios;


use App\Http\Controllers\convenios\Repository\ConvenioRepository;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class ConveniosController extends Controller
{
    public function getBuscarConvenioExistente(ConvenioRepository $repo, Request $request)
    {
        return response()->json($repo->findByConvenioPrestador($request->id), 200);
    }

}
