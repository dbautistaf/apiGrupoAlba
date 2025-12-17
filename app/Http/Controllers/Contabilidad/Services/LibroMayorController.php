<?php

namespace App\Http\Controllers\Contabilidad\Services;

use App\Http\Controllers\Contabilidad\Repository\LibroMayorRepository;
use App\Http\Controllers\Contabilidad\Repository\PeriodosContablesRepository;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\View;

class LibroMayorController extends Controller
{

    private $libroMayorRepository;
    private $periodoContableRepositorio;

    public function __construct(
        LibroMayorRepository $libroMayorRepository,
        PeriodosContablesRepository $periodoContableRepositorio
    ) {
        $this->libroMayorRepository = $libroMayorRepository;
        $this->periodoContableRepositorio = $periodoContableRepositorio;
    }

    /**
     * Generar el libro mayor con los filtros especificados
     */
    public function getLibroMayor(Request $request)
    {
        try {
            // El frontend manda id_periodo_contable requerido; si no lo hace aceptamos rango de fechas
            if (is_null($request->id_periodo_contable) && (is_null($request->desde) || is_null($request->hasta))) {
                return response()->json([
                    'message' => 'Debe especificar un período contable o un rango de fechas para generar el libro mayor.'
                ], 422);
            }

            // Si no se especifica período pero sí fechas, usar el período activo
            if (is_null($request->id_periodo_contable) && !is_null($request->desde)) {
                $periodoActivo = $this->periodoContableRepositorio->findByPeriodoContableActivo();
                if ($periodoActivo) {
                    $request->merge(['id_periodo_contable' => $periodoActivo->id_periodo_contable]);
                }
            }

            // Generar el libro mayor agrupado por cuenta
            $libroMayor = $this->libroMayorRepository->findByResumenPorCuenta($request);

            // Devolver el array directamente (el frontend espera un array de cuentas con movimientos)
            return response()->json($libroMayor, 200);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al generar el libro mayor: ' . $e->getMessage()
            ], 500);
        }
    }


    /**
     * Generar el reporte del libro mayor listo para PDF
     */
    public function getReporteLibroMayor(Request $request)
    {
        try {
            // Validación mínima (mismo criterio que getLibroMayor)
            if (is_null($request->id_periodo_contable) && (is_null($request->desde) || is_null($request->hasta))) {
                return response()->json([
                    'message' => 'Debe especificar un período contable o un rango de fechas para generar el libro mayor.'
                ], 422);
            }

            // Usar periodo activo si no se especificó
            if (is_null($request->id_periodo_contable) && !is_null($request->desde)) {
                $periodoActivo = $this->periodoContableRepositorio->findByPeriodoContableActivo();
                if ($periodoActivo) {
                    $request->merge(['id_periodo_contable' => $periodoActivo->id_periodo_contable]);
                }
            }

            // Generar reporte (puedes pasar info de empresa si la tienes)
            $reporte = $this->libroMayorRepository->findByReporteLibroMayor($request);

            // Renderizar la vista Blade a HTML
            $html = View::make('reportes.pdflibromayor', compact('reporte'))->render();

            // Configurar mPDF y generar PDF
            $mpdf = new \Mpdf\Mpdf([
                'default_font' => 'quicksand',
                'format' => 'A4',
                'margin_top' => 5,
                'margin_bottom' => 5,
                'margin_left' => 6,
                'margin_right' => 6
            ]);

            $mpdf->fontdata['quicksand'] = [
                'R' => 'resources/fonts/Quicksand-Regular.ttf',
                'B' => 'resources/fonts/Quicksand-Bold.ttf',
                'I' => 'resources/fonts/Quicksand-Light.ttf',
                'BI' => 'resources/fonts/Quicksand-SemiBold.ttf'
            ];

            $mpdf->showWatermarkText = false;
            $mpdf->WriteHTML($html);

            $pdfOutput = $mpdf->Output('libro-mayor.pdf', 'S');

            return response($pdfOutput, 200)
                ->header('Content-Type', 'application/pdf')
                ->header('Content-Disposition', 'attachment; filename="libro-mayor.pdf"');
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al generar el reporte libro mayor: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Calcular totales generales del libro mayor
     */
    private function calcularTotalesGenerales($libroMayor)
    {
        $totalDebe = 0;
        $totalHaber = 0;
        $totalSaldoAnterior = 0;

        foreach ($libroMayor as $cuenta) {
            $totalDebe += $cuenta['total_debe'];
            $totalHaber += $cuenta['total_haber'];
            if (isset($cuenta['saldo_anterior'])) {
                $totalSaldoAnterior += $cuenta['saldo_anterior']['saldo_anterior'];
            }
        }

        return [
            'total_debe' => $totalDebe,
            'total_haber' => $totalHaber,
            'total_saldo_anterior' => $totalSaldoAnterior,
            'diferencia' => $totalDebe - $totalHaber,
            'cantidad_cuentas' => count($libroMayor)
        ];
    }
}
