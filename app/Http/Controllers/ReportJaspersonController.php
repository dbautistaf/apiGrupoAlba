<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use PHPJasper\PHPJasper;

class ReportJaspersonController extends Controller
{

    public function index()
    {
        $input = storage_path('app/public/reports/rpt_prestacion.jrxml');
        $output = storage_path('app/public/reports');
        $ruta = storage_path('app/public/');
        $options = [
            'format' => ['pdf'],
            'locale' => 'en',
            'params' => ['P_RUTA' => $ruta],
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
