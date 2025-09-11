<?php

namespace App\Http\Controllers\mantenimiento;

use App\Models\DetallePrestacionesPracticaLaboratorioEntity;
use App\Models\PrestacionesPracticaLaboratorioEntity;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use PHPJasper\PHPJasper;

class PrestacionesController extends Controller
{


    public function getBuscarPrestacionesDNI(Request $request)
    {
        $data = PrestacionesPracticaLaboratorioEntity::with(["detalle",   "estadoPrestacion", "afiliado", "usuario", "prestador", "profesional"])
            ->where('dni_afiliado',  $request->dni)
            ->orderByDesc('cod_prestacion')
            ->get();

        foreach ($data as $objeto) {
            $objeto->setAttribute('show', false);
        }

        return response()->json($data, 200);
    }

    public function getImprimirReporte(Request $request)
    {
        $input = storage_path('app/public/reports/rpt_prestacion.jrxml');
        $output = storage_path('app/public/reports');
        $ruta = storage_path('app/public/');
        $options = [
            'format' => ['pdf'],
            'locale' => 'en',
            'params' => ['P_RUTA' => $ruta, 'P_ID' => $request->prestacion],
            'db_connection' => [
                'driver' => 'mysql',
                'username' => env('DB_USERNAME'),
                'password' => env('DB_PASSWORD'),
                'host' => env('DB_HOST'),
                'database' => env('DB_DATABASE'),
                'port' => env('DB_PORT')
            ]
        ];

        $jasper = new PHPJasper;

        $jasper->process(
            $input,
            $output,
            $options
        )->execute();

        return response()->file(storage_path('app/public/reports/rpt_prestacion.pdf'), ['Content-Type' => 'application/pdf']);
    }
}
