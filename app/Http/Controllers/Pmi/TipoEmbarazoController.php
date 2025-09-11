<?php

namespace App\Http\Controllers\Pmi;

use App\Http\Controllers\Controller;
use App\Models\Pmi\TipoEmbarazoModel;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as RoutingController;

class TipoEmbarazoController extends RoutingController
{
    //
    public function getEmbarazo(){
        $query =  TipoEmbarazoModel::where('estado', 1)->orderBy('descripcion_tipo','asc')->get();
        return response()->json($query, 200);
    }
}
