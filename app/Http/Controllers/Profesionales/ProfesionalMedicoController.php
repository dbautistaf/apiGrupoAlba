<?php

namespace App\Http\Controllers\Profesionales;

use App\Http\Controllers\Profesionales\Repository\ProfesionalMedicoRepository;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class ProfesionalMedicoController extends Controller {

    public function getRegistroRapido(ProfesionalMedicoRepository $repo, Request $request){
        return response()->json($repo->findByRegistroRapido($request));
    }
}
