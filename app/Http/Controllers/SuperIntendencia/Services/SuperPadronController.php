<?php

namespace App\Http\Controllers\SuperIntendencia\Services;

use App\Models\SuperIntendencia\AdhesionAfipEntity;
use App\Models\SuperIntendencia\AltasMonotributoEntity;
use App\Models\SuperIntendencia\AltasRegimenGeneralEntity;
use App\Models\SuperIntendencia\BajaAutomaticaAfipEntity;
use App\Models\SuperIntendencia\BajasMonotributoEntity;
use App\Models\SuperIntendencia\BajasRegimenGeneralEntity;
use App\Models\SuperIntendencia\DesempleoSuperIntendenciaEntity;
use App\Models\SuperIntendencia\EfectoresSocialesEntity;
use App\Models\SuperIntendencia\ExpedientesEntity;
use App\Models\SuperIntendencia\FamiliaresMonotributoEntity;
use App\Models\SuperIntendencia\SuperPadronEntity;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class SuperPadronController extends Controller
{

    public function getListSuperPadron(Request $request)
    {
        if ($request->nombre == '') {
            if ($request->tipo == '1' && $request->periodo == '') {
                $query =  SuperPadronEntity::get();
                return response()->json($query, 200);
            } elseif ($request->tipo == '2' && $request->periodo == '') {
                $query =  DesempleoSuperIntendenciaEntity::get();
                return response()->json($query, 200);
            } elseif ($request->tipo == '3' && $request->periodo == '') {
                $query =  AdhesionAfipEntity::get();
                return response()->json($query, 200);
            } elseif ($request->tipo == '4' && $request->periodo == '') {
                $query =  BajaAutomaticaAfipEntity::get();
                return response()->json($query, 200);
            } elseif ($request->tipo == '5' && $request->periodo == '') {
                $query =  FamiliaresMonotributoEntity::get();
                return response()->json($query, 200);
            } elseif ($request->tipo == '6' && $request->periodo == '') {
                $query =  EfectoresSocialesEntity::get();
                return response()->json($query, 200);
            } elseif ($request->tipo == '7' && $request->periodo == '') {
                $query =  AltasRegimenGeneralEntity::get();
                return response()->json($query, 200);
            } elseif ($request->tipo == '8' && $request->periodo == '') {
                $query =  BajasRegimenGeneralEntity::get();
                return response()->json($query, 200);
            } elseif ($request->tipo == '9' && $request->periodo == '') {
                $query =  AltasMonotributoEntity::get();
                return response()->json($query, 200);
            } elseif ($request->tipo == '10' && $request->periodo == '') {
                $query =  BajasMonotributoEntity::get();
                return response()->json($query, 200);
            } elseif ($request->tipo == '11' && $request->periodo == '') {
                $query =  ExpedientesEntity::get();
                return response()->json($query, 200);
            } elseif ($request->tipo == '1' && $request->periodo != '') {
                $query =  SuperPadronEntity::where('periodo', $request->periodo)->get();
                return response()->json($query, 200);
            } elseif ($request->tipo == '2' && $request->periodo != '') {
                $query =  DesempleoSuperIntendenciaEntity::where('periodo_importacion', $request->periodo)->get();
                return response()->json($query, 200);
            } elseif ($request->tipo == '3' && $request->periodo != '') {
                $query =  AdhesionAfipEntity::where('periodo_import', $request->periodo)->get();
                return response()->json($query, 200);
            } elseif ($request->tipo == '4' && $request->periodo != '') {
                $query =  BajaAutomaticaAfipEntity::where('periodo_import', $request->periodo)->get();
                return response()->json($query, 200);
            } elseif ($request->tipo == '5' && $request->periodo != '') {
                $query =  FamiliaresMonotributoEntity::where('periodo_importacion', $request->periodo)->get();
                return response()->json($query, 200);
            } elseif ($request->tipo == '6' && $request->periodo != '') {
                $query =  EfectoresSocialesEntity::where('periodo_importacion', $request->periodo)->get();
                return response()->json($query, 200);
            } elseif ($request->tipo == '7' && $request->periodo != '') {
                $query =  AltasRegimenGeneralEntity::where('periodo', $request->periodo)->get();
                return response()->json($query, 200);
            } elseif ($request->tipo == '8' && $request->periodo != '') {
                $query =  BajasRegimenGeneralEntity::where('periodo', $request->periodo)->get();
                return response()->json($query, 200);
            } elseif ($request->tipo == '9' && $request->periodo != '') {
                $query =  AltasMonotributoEntity::where('periodo', $request->periodo)->get();
                return response()->json($query, 200);
            } elseif ($request->tipo == '10' && $request->periodo != '') {
                $query =  BajasMonotributoEntity::where('periodo', $request->periodo)->get();
                return response()->json($query, 200);
            } elseif ($request->tipo == '11' && $request->periodo != '') {
                $query =  ExpedientesEntity::where('periodo', $request->periodo)->get();
                return response()->json($query, 200);
            }
        } else {
            if ($request->tipo == '1') {
                $query =  SuperPadronEntity::where('dni', 'LIKE', "$request->nombre%")
                    ->orWhere('nombres', 'LIKE', "$request->nombre%")->get();
                return response()->json($query, 200);
            } elseif ($request->tipo == '2') {
                $query =  DesempleoSuperIntendenciaEntity::where('nro_documento', 'LIKE', "$request->nombre%")
                    ->orWhere('nombres', 'LIKE', "$request->nombre%")->get();
                return response()->json($query, 200);
            } elseif ($request->tipo == '3') {
                $query =  AdhesionAfipEntity::where('cuit', 'LIKE', "$request->nombre%")
                    ->orWhere('nombres', 'LIKE', "$request->nombre%")->get();
                return response()->json($query, 200);
            } elseif ($request->tipo == '4') {
                $query =  BajaAutomaticaAfipEntity::where('cuit', 'LIKE', "$request->nombre%")
                    ->orWhere('nombres', 'LIKE', "$request->nombre%")->get();
                return response()->json($query, 200);
            } elseif ($request->tipo == '5') {
                $query =  FamiliaresMonotributoEntity::where('nro_documento_fam', 'LIKE', "$request->nombre%")
                    ->orWhere('nombres_fam', 'LIKE', "$request->nombre%")->get();
                return response()->json($query, 200);
            } elseif ($request->tipo == '6') {
                $query =  EfectoresSocialesEntity::where('cuit_titular', 'LIKE', "$request->nombre%")
                    ->orWhere('nombres_efector', 'LIKE', "$request->nombre%")->get();
                return response()->json($query, 200);
            } elseif ($request->tipo == '7') {
                $query =  AltasRegimenGeneralEntity::where('cuil_titular', 'LIKE', "$request->nombre%")
                    ->orWhere('nombres', 'LIKE', "$request->nombre%")->get();
                return response()->json($query, 200);
            } elseif ($request->tipo == '8') {
                $query =  BajasRegimenGeneralEntity::where('cuil_titular', 'LIKE', "$request->nombre%")
                    ->orWhere('nombres', 'LIKE', "$request->nombre%")->get();
                return response()->json($query, 200);
            } elseif ($request->tipo == '9') {
                $query =  AltasMonotributoEntity::where('cuil', 'LIKE', "$request->nombre%")
                    ->orWhere('nombres', 'LIKE', "$request->nombre%")->get();
                return response()->json($query, 200);
            } elseif ($request->tipo == '10') {
                $query =  BajasMonotributoEntity::where('cuil', 'LIKE', "$request->nombre%")
                    ->orWhere('nombres', 'LIKE', "$request->nombre%")->get();
                return response()->json($query, 200);
            } elseif ($request->tipo == '11') {
                $query =  ExpedientesEntity::where('cuil_tit', 'LIKE', "$request->nombre%")
                    ->orWhere('nombres', 'LIKE', "$request->nombre%")->get();
                return response()->json($query, 200);
            }
        }
    }
}
