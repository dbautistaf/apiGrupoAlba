<?php

namespace  App\Http\Controllers\SuperIntendencia\Services;

use App\Models\SuperIntendencia\DesempleoSuperIntendenciaEntity;
use Illuminate\Routing\Controller;

class DesempleoController extends Controller
{
    public function getSuperPadron(){
        $query =  DesempleoSuperIntendenciaEntity::get();
        return response()->json($query, 200);
    }
}
