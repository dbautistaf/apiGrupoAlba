<?php

namespace  App\Http\Controllers\SuperIntendencia\Services;

use App\Models\SuperIntendencia\AdhesionAfipEntity;
use Illuminate\Routing\Controller;

class AdhesionAfipController extends Controller
{
    public function getAdhesionAfip()
    {
        $query =  AdhesionAfipEntity::get();
        return response()->json($query, 200);
    }
}
