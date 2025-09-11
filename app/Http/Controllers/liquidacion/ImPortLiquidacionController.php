<?php

namespace App\Http\Controllers\liquidacion;

use App\Exports\LiquidacionOscearaExport;
use App\Exports\LiquidacionOsetyaExport;
use App\Exports\LiquidacionOsfotExport;
use App\Exports\LiquidacionOsmitaExport;
use App\Exports\LiquidacionOsycExport;
use App\Exports\LiquidacionPrensaExport;
use App\Imports\LiquidacionImport;
use App\Models\liquidacion\LiquidacionObrasSociales;
use App\Models\liquidacion\LiquidacionOsceara;
use App\Models\liquidacion\LiquidacionOsetya;
use App\Models\liquidacion\LiquidacionOsfotModel;
use App\Models\liquidacion\LiquidacionOsmitaModel;
use App\Models\liquidacion\LiquidacionOsycModel;
use App\Models\liquidacion\LiquidacionPrensa;
use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class ImPortLiquidacionController extends Controller
{
    //

    public function saveLiquidacion(Request $request)
    {
        $archivo = $request->file('file');
        if ($archivo) {
            try {
                DB::beginTransaction();
                $importacion = new LiquidacionImport($request->tipo_archivo);
                Excel::import($importacion, $archivo);
                DB::commit();
                $mensaje = $importacion->getMensaje();
                return response()->json(['message' => $mensaje], 200);
            } catch (\Throwable $exception) {
                DB::rollBack();
                return response()->json(['message' => $exception->getMessage()], 500);
            }
        } else {
            return response()->json(['message' => 'No se encontro ningun archivo'], 500);
        }
    }

    public function listLiquidacion(Request $request)
    {

        $query = [];
        switch ($request->tipo) {
            case '1':
                $model = LiquidacionOsycModel::with('PadronAfil');
                $cuilField = 'CUIL';
                $cuitField = 'CUIT';
                break;
            case '2':
                $model = LiquidacionOsmitaModel::with('PadronAfil');
                $cuilField = 'cuil';
                $cuitField = 'cuit';
                break;
            case '3':
                $model = LiquidacionOsfotModel::with('PadronAfil');
                $cuilField = 'CUIL';
                $cuitField = 'CUIT';
                break;
            case '4':
                $model = LiquidacionPrensa::with('PadronAfil');
                $cuilField = 'cuitapo';
                $cuitField = 'cuitcont';
                break;
            case '5':
                $model = LiquidacionOsceara::with('PadronAfil');
                $cuilField = 'cuil';
                $cuitField = 'cuit';
                break;
            case '6':
                $model = LiquidacionOsetya::with('PadronAfil');
                $cuilField = 'cuitapo';
                $cuitField = 'cuitcont';
                break;
            case '0':
                $model = LiquidacionObrasSociales::with('PadronAfil');
                $cuilField = 'cuil';
                $cuitField = 'cuit';
                break;
            default:
                return response()->json(['message' => 'Tipo no válido'], 400);
        }
        $query = $model;
        if (empty($request->cuit) && empty($request->cuil) && empty($request->unidad)) {
            $query = $query->limit(50);
        }

        if (!empty($request->cuit) && $cuitField) {
            $query = $query->where($cuitField, 'LIKE', "$request->cuit%");
        }

        if (!empty($request->cuil) && $cuilField) {
            $query = $query->where($cuilField, 'LIKE', "$request->cuil%");
        }

        if (!empty($request->unidad)) {
            $query = $query->whereHas('PadronAfil', function ($q) use ($request) {
                $q->where('id_unidad_negocio', '=', "$request->unidad");
            });
        }
        $query = $query->with('PadronAfil');
        $result = $query->get();
        return response()->json($result, 200);
    }

    public function listLikeCuilLiquidacion(Request $request)
    {
        $query = '';
        if ($request->tipo == '1'  && $request->cuil != '') {
            $query = LiquidacionOsycModel::where('cuil', 'LIKE', "$request->cuil%")->limit(100)->get();
        } elseif ($request->tipo == '2'  && $request->cuil != '') {
            $query = LiquidacionOsmitaModel::where('cuil', 'LIKE', "$request->cuil%")->limit(100)->get();
        } elseif ($request->tipo == '3'  && $request->cuil != '') {
            $query = LiquidacionOsfotModel::where('cuil', 'LIKE', "$request->cuil%")->limit(100)->get();
        }
        return response()->json($query, 200);
    }

    public function listLikeCuitLiquidacion(Request $request)
    {
        $query = '';
        if ($request->tipo == '1'  && $request->cuit != '') {
            $query = LiquidacionOsycModel::where('cuit', 'LIKE', "$request->cuit%")->limit(100)->get();
        } elseif ($request->tipo == '2'  && $request->cuit != '') {
            $query = LiquidacionOsmitaModel::where('cuit', 'LIKE', "$request->cuit%")->limit(100)->get();
        } elseif ($request->tipo == '3'  && $request->cuit != '') {
            $query = LiquidacionOsfotModel::where('cuit', 'LIKE', "$request->cuit%")->limit(100)->get();
        }
        return response()->json($query, 200);
    }

    public function exportLiquidacion(Request $request)
    {
        try {
            if ($request->tipo == '1') {
                return Excel::download(new LiquidacionOsycExport($request), 'OSYC.xlsx');
            } elseif ($request->tipo == '2') {
                return Excel::download(new LiquidacionOsmitaExport($request), 'OSMITA.xlsx');
            } elseif ($request->tipo == '3') {
                return Excel::download(new LiquidacionOsfotExport($request), 'OSFOT.xlsx');
            } elseif ($request->tipo == '4') {
                return Excel::download(new LiquidacionPrensaExport($request), 'PRENSA.xlsx');
            } elseif ($request->tipo == '5') {
                return Excel::download(new LiquidacionOscearaExport($request), 'OSCEARA.xlsx');
            } elseif ($request->tipo == '6') {
                return Excel::download(new LiquidacionOsetyaExport($request), 'OSETYA.xlsx');
            } else {
                return response()->json(['error' => 'Tipo no válido'], 400);
            }
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
