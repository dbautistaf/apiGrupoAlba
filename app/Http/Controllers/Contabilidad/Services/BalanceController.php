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
            // validar periodo
            if (empty($request->id_periodo_contable)) {
                return response()->json(['message' => 'Seleccione el Período Contable antes de buscar'], 422);
            }

            // normalizar period activo si es necesario
            if (empty($request->id_periodo_contable) && !empty($request->desde)) {
                $periodoActivo = $this->periodoContableRepositorio->findByPeriodoContableActivo();
                if ($periodoActivo) {
                    $request->merge(['id_periodo_contable' => $periodoActivo->id_periodo_contable]);
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
            if (empty($request->id_periodo_contable) && (empty($request->desde) || empty($request->hasta))) {
                return response()->json(['message' => 'Debe especificar un período contable o un rango de fechas'], 422);
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

            // Obtener el periodo activo si no viene en el request
            $periodoActivo = null;
            if (empty($request->id_periodo_contable) && !empty($request->desde)) {
                $periodoActivo = $this->periodoContableRepositorio->findByPeriodoContableActivo();
                if ($periodoActivo) {
                    $filtros['id_periodo_contable'] = $periodoActivo->id_periodo_contable;
                    $filtros['anio_periodo'] = $periodoActivo->anio_periodo;
                }
            } else if (!empty($request->id_periodo_contable)) {
                $periodoActivo = \App\Models\Contabilidad\PeriodosContablesEntity::find($request->id_periodo_contable);
                if ($periodoActivo) {
                    $filtros['anio_periodo'] = $periodoActivo->anio_periodo;
                }
            }

            // Pasar todos los filtros al repositorio
            $reporte = $this->balanceRepository->findByReporteBalance($filtros);

            // Si no hay cuentas, retorna PDF vacío o mensaje
            if (empty($reporte['cuentas'])) {
                // Puedes retornar un PDF con mensaje o un JSON, aquí ejemplo PDF con mensaje
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