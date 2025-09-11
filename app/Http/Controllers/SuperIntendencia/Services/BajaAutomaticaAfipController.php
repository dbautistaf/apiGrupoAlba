<?php

namespace  App\Http\Controllers\SuperIntendencia\Services;

use App\Models\SuperIntendencia\BajaAutomaticaAfipEntity;
use Illuminate\Routing\Controller;

class BajaAutomaticaAfipController extends Controller
{
    public function getBajaAUtomatica()
    {
        $query =  BajaAutomaticaAfipEntity::get();
        return response()->json($query, 200);
    }
}
