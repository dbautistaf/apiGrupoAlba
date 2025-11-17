<?php

namespace App\Http\Controllers\Notificaciones\Services;

use App\Http\Controllers\Notificaciones\Repository\NotificacionesRepository;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class NotificacionesController extends Controller
{

    protected $repo;

    public function __construct(NotificacionesRepository $repo)
    {
        $this->repo = $repo;
    }

    public function listar(Request $request)
    {
        $user = auth()->user();
        return response()->json($this->repo->findByListar($user->email));
    }
}
