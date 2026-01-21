<?php

namespace App\Http\Controllers\Contabilidad\Services;

use App\Http\Controllers\Contabilidad\Repository\BalanceRepository;
use App\Http\Controllers\Contabilidad\Repository\PeriodosContablesRepository;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\View;

class BalanceController extends Controller
{
    private $balanceRepository;
    private $periodoContableRepositorio;

    public function __construct(
        BalanceRepository $balanceRepository,
        PeriodosContablesRepository $periodoContableRepositorio
    ) {
        $this->balanceRepository = $balanceRepository;
        $this->periodoContableRepositorio = $periodoContableRepositorio;
    }

    /**
     * Endpoint: GET api/v1/contabilidad/getBalanceSaldo
     * Retorna array con cuentas y movimientos (estructura que espera el frontend)
     */
    public function getBalanceSaldo(Request $request)
    {
        try {
            // Validar que se especifique al menos un criterio de filtro temporal
            if (is_null($request->id_periodo_contable) && (is_null($request->desde) || is_null($request->hasta))) {
                return response()->json([
                    'message' => 'Debe especificar un período contable o un rango de fechas para generar el balance.'
                ], 422);
            }

            // Si viene período contable, obtener sus fechas para el cálculo del saldo anterior
            if (!is_null($request->id_periodo_contable)) {
                $periodo = $this->periodoContableRepositorio->findById($request->id_periodo_contable);

                if ($periodo) {
                    // Mantener el período para los movimientos, pero establecer fechas para saldo anterior
                    if ($periodo->id_tipo_periodo === 1) {
                        // Período mensual: usar fechas del mes
                        $request->merge([
                            'fecha_inicio_periodo' => $periodo->periodo_inicio,
                            'fecha_fin_periodo' => $periodo->periodo_fin
                        ]);
                    } elseif ($periodo->id_tipo_periodo === 2) {
                        // Período anual: usar fechas del año
                        $request->merge([
                            'fecha_inicio_periodo' => $periodo->anio_periodo . '-01-01',
                            'fecha_fin_periodo' => $periodo->anio_periodo . '-12-31'
                        ]);
                    }
                }
            }

            // Adaptar filtro de nivel según lo que llega del frontend
            $filtros = $request->all();
            if (isset($filtros['nivel'])) {
                $nivelFront = trim($filtros['nivel']);
                $nivelesMap = [
                    'Grupo' => 'GRUPO',
                    'Sub Grupo' => 'SUBGRUPO',
                    'Cuenta' => 'CUENTA',
                    'Subcuenta' => 'SUBCUENTA',
                    'Subcuenta 1' => 'SUBCUENTA 1'
                ];
                if ($nivelFront === 'Todos') {
                    unset($filtros['nivel']); // No filtrar por nivel
                } elseif (isset($nivelesMap[$nivelFront])) {
                    $filtros['nivel'] = $nivelesMap[$nivelFront];
                } else {
                    unset($filtros['nivel']); // Si no coincide, no filtrar
                }
            }

            // Pasar todos los filtros del request al repositorio
            $resultado = $this->balanceRepository->findByBalanceSaldo($filtros);
            return response()->json($resultado, 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error al obtener balance: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Endpoint: GET api/v1/contabilidad/getExportarBalanceSaldo
     * Genera PDF del balance y lo retorna como attachment (mPDF)
     */
    public function getExportarBalanceSaldo(Request $request)
    {
        try {
            // Validar que se especifique al menos un criterio de filtro temporal
            if (is_null($request->id_periodo_contable) && (is_null($request->desde) || is_null($request->hasta))) {
                return response()->json([
                    'message' => 'Debe especificar un período contable o un rango de fechas para generar el balance.'
                ], 422);
            }

            // Si viene período contable, obtener sus fechas para el cálculo del saldo anterior
            $periodoInfo = null;
            if (!is_null($request->id_periodo_contable)) {
                $periodo = $this->periodoContableRepositorio->findById($request->id_periodo_contable);

                if ($periodo) {
                    $periodoInfo = $periodo;
                    // Mantener el período para los movimientos, pero establecer fechas para saldo anterior
                    if ($periodo->id_tipo_periodo === 1) {
                        // Período mensual: usar fechas del mes
                        $request->merge([
                            'fecha_inicio_periodo' => $periodo->periodo_inicio,
                            'fecha_fin_periodo' => $periodo->periodo_fin,
                            'desde' => $periodo->periodo_inicio,
                            'hasta' => $periodo->periodo_fin
                        ]);
                    } elseif ($periodo->id_tipo_periodo === 2) {
                        // Período anual: usar fechas del año
                        $request->merge([
                            'fecha_inicio_periodo' => $periodo->anio_periodo . '-01-01',
                            'fecha_fin_periodo' => $periodo->anio_periodo . '-12-31',
                            'desde' => $periodo->anio_periodo . '-01-01',
                            'hasta' => $periodo->anio_periodo . '-12-31'
                        ]);
                    }
                }
            }

            // Crear un array con todos los parámetros del request
            $filtros = $request->all();

            // Adaptar filtro de nivel igual que en getBalanceSaldo
            if (isset($filtros['nivel'])) {
                $nivelFront = trim($filtros['nivel']);
                $nivelesMap = [
                    'Grupo' => 'GRUPO',
                    'Sub Grupo' => 'SUBGRUPO',
                    'Cuenta' => 'CUENTA',
                    'Subcuenta' => 'SUBCUENTA',
                    'Subcuenta 1' => 'SUBCUENTA 1'
                ];
                if ($nivelFront === 'Todos') {
                    unset($filtros['nivel']);
                } elseif (isset($nivelesMap[$nivelFront])) {
                    $filtros['nivel'] = $nivelesMap[$nivelFront];
                } else {
                    unset($filtros['nivel']);
                }
            }

            // Establecer las fechas para el encabezado del reporte
            if ($periodoInfo) {
                // Si es por período, usar las fechas del período
                $filtros['desde_reporte'] = $periodoInfo->periodo_inicio;
                $filtros['hasta_reporte'] = $periodoInfo->periodo_fin;
                $filtros['anio_periodo'] = $periodoInfo->anio_periodo;
                $filtros['periodo_contable'] = $periodoInfo->periodo_contable;
            } else {
                // Si es por fechas, usar las fechas originales
                $filtros['desde_reporte'] = $request->desde;
                $filtros['hasta_reporte'] = $request->hasta;
            }

            // Pasar todos los filtros al repositorio
            $reporte = $this->balanceRepository->findByReporteBalance($filtros);

            // Si no hay cuentas, retorna PDF vacío o mensaje
            if (empty($reporte['cuentas'])) {
                $html = '<h3>No existen cuentas con movimiento para los filtros seleccionados.</h3>';
                $mpdf = new \Mpdf\Mpdf([
                    'default_font' => 'quicksand',
                    'format' => 'A4',
                    'margin_top' => 5,
                    'margin_bottom' => 5,
                    'margin_left' => 6,
                    'margin_right' => 6
                ]);
                $mpdf->WriteHTML($html);
                $pdfOutput = $mpdf->Output('balance-saldo.pdf', 'S');
                return response($pdfOutput, 200)
                    ->header('Content-Type', 'application/pdf')
                    ->header('Content-Disposition', 'attachment; filename="balance-saldo.pdf"');
            }

            // render view using the new pdfbalance template
            $html = View::make('reportes.pdfbalance', compact('reporte'))->render();

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

            $mpdf->WriteHTML($html);
            $pdfOutput = $mpdf->Output('balance-saldo.pdf', 'S');

            return response($pdfOutput, 200)
                ->header('Content-Type', 'application/pdf')
                ->header('Content-Disposition', 'attachment; filename="balance-saldo.pdf"');
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error al exportar balance: ' . $e->getMessage()], 500);
        }
    }
}