<?php

namespace App\Http\Controllers\prestadores;

use App\Exports\Prestadores\PrestadoresFacturasImpagasExport;
use Illuminate\Routing\Controller;
use App\Http\Controllers\prestadores\repository\ReportesPrestadorRepository;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ReportesPrestadorController extends Controller
{
    protected $reportesRepository;

    public function __construct(ReportesPrestadorRepository $reportesRepository)
    {
        $this->reportesRepository = $reportesRepository;
    }

    public function exportarExcelFacturasImpagas(Request $request)
    {
        try {
            $fechaInicio = $request->get('fecha_inicio');
            $fechaFin = $request->get('fecha_fin');

            // Validar fechas si se proporcionan
            if ($fechaInicio) {
                $fechaInicio = Carbon::parse($fechaInicio)->format('Y-m-d');
            }

            if ($fechaFin) {
                $fechaFin = Carbon::parse($fechaFin)->format('Y-m-d');
            }

            $prestadores = $this->reportesRepository->getPrestadoresConFacturasImpagas($fechaInicio, $fechaFin);

            $filename = 'prestadores_facturas_impagas_' . date('Y-m-d_H-i-s') . '.xlsx';

            return Excel::download(
                new PrestadoresFacturasImpagasExport($prestadores),
                $filename
            );
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Error al generar el reporte: ' . $e->getMessage()
            ], 500);
        }
    }

    public function exportarExcelResumenFacturasImpagas(Request $request)
    {
        try {
            $fechaInicio = $request->get('fecha_inicio');
            $fechaFin = $request->get('fecha_fin');

            if ($fechaInicio) {
                $fechaInicio = Carbon::parse($fechaInicio)->format('Y-m-d');
            }

            if ($fechaFin) {
                $fechaFin = Carbon::parse($fechaFin)->format('Y-m-d');
            }

            $resumen = $this->reportesRepository->getResumenFacturasImpagas($fechaInicio, $fechaFin);

            $filename = 'resumen_prestadores_facturas_impagas_' . date('Y-m-d_H-i-s') . '.xlsx';

            return Excel::download(
                new PrestadoresFacturasImpagasExport($resumen, 'resumen'),
                $filename
            );
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Error al generar el resumen: ' . $e->getMessage()
            ], 500);
        }
    }
}
