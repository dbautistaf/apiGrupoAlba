<?php

namespace App\Http\Controllers\Contabilidad\Services;

use App\Http\Controllers\Contabilidad\Repository\LibroDiarioRepository;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\View;

class LibroDiarioController extends Controller
{

    public function getListarResumenDiario(Request $request, LibroDiarioRepository $libroDiarioRepository)
    {
        $data = $libroDiarioRepository->findListDetalleResumenDiario($request);
        $totalItems = $libroDiarioRepository->getTotalCount($request);

        // Si los datos vienen paginados
        if (method_exists($data, 'items')) {
            $dtListaData = [];
            foreach ($data->items() as $value) {
                $detalle = [];
                foreach ($value->detalle as $key) {
                    $detalle[] = array(
                        'cuenta' => $key->planCuenta->codigo_cuenta . ' - ' . $key->planCuenta->cuenta,
                        'debe' => (float) $key->monto_debe > 0 ? $key->monto_debe : '',
                        'haber' => (float) $key->monto_haber > 0 ? $key->monto_haber : '',
                        'recursor' => $key->recursor
                    );
                }
                $dtListaData[] = array(
                    'id_asiento_contable' => $value->id_asiento_contable,
                    'periodo_contable' => $value->periodoContable->periodo,
                    'fecha' => $value->fecha_asiento,
                    'numero' => $value->numero,
                    'leyenda' => $value->asiento_leyenda,
                    'cuentas' => $detalle
                );
            }

            return response()->json([
                'data' => $dtListaData,
                'totalItems' => $totalItems,
                'pagination' => [
                    'current_page' => $data->currentPage(),
                    'last_page' => $data->lastPage(),
                    'per_page' => $data->perPage(),
                    'total' => $data->total(),
                    'from' => $data->firstItem(),
                    'to' => $data->lastItem()
                ]
            ]);
        } else {
            // Si no hay paginación (respuesta tradicional)
            $dtListaData = [];
            foreach ($data as $value) {
                $detalle = [];
                foreach ($value->detalle as $key) {
                    $detalle[] = array(
                        'cuenta' => $key->planCuenta->codigo_cuenta . ' - ' . $key->planCuenta->cuenta,
                        'debe' => (float) $key->monto_debe > 0 ? $key->monto_debe : '',
                        'haber' => (float) $key->monto_haber > 0 ? $key->monto_haber : '',
                        'recursor' => $key->recursor
                    );
                }
                $dtListaData[] = array(
                    'id_asiento_contable' => $value->id_asiento_contable,
                    'periodo_contable' => $value->periodoContable->periodo,
                    'fecha' => $value->fecha_asiento,
                    'numero' => $value->numero,
                    'leyenda' => $value->asiento_leyenda,
                    'cuentas' => $detalle
                );
            }

            return response()->json([
                'data' => $dtListaData,
                'totalItems' => $totalItems
            ]);
        }
    }

    /**
     * Generar el reporte del libro diario listo para PDF
     */
    public function getReporteLibroDiario(Request $request, LibroDiarioRepository $libroDiarioRepository)
    {
        try {
            // Obtener los datos del libro diario
            $data = $libroDiarioRepository->findListDetalleResumenDiario($request);

            $asientos = [];
            $totalDebe = 0;
            $totalHaber = 0;

            foreach ($data as $value) {
                $detalle = [];
                $asientoDebe = 0;
                $asientoHaber = 0;

                foreach ($value->detalle as $key) {
                    $debe = (float) $key->monto_debe;
                    $haber = (float) $key->monto_haber;

                    $detalle[] = [
                        'cuenta' => $key->planCuenta->codigo_cuenta . ' - ' . $key->planCuenta->cuenta,
                        'debe' => $debe,
                        'haber' => $haber,
                        'recurso' => $key->recursor
                    ];

                    $asientoDebe += $debe;
                    $asientoHaber += $haber;
                }

                $asientos[] = [
                    'id_asiento_contable' => $value->id_asiento_contable,
                    'fecha' => $value->fecha_asiento,
                    'numero' => $value->numero,
                    'leyenda' => $value->asiento_leyenda,
                    'cuentas' => $detalle,
                    'total_debe' => $asientoDebe,
                    'total_haber' => $asientoHaber
                ];

                $totalDebe += $asientoDebe;
                $totalHaber += $asientoHaber;
            }

            // Preparar datos para el reporte
            $reporte = [
                'encabezado' => [
                    'fecha_generacion' => now()->format('Y-m-d H:i:s'),
                    'filtros' => [
                        'id_periodo_contable' => $request->id_periodo_contable ?? '-',
                        'periodo_contable' => $data[0]->periodoContable->periodo ?? '-',
                        'desde' => $request->desde ?? '-',
                        'hasta' => $request->hasta ?? '-',
                        'numero_desde' => $request->numero_desde ?? '',
                        'numero_hasta' => $request->numero_hasta ?? ''
                    ]
                ],
                'asientos' => $asientos,
                'totales' => [
                    'total_debe' => $totalDebe,
                    'total_haber' => $totalHaber,
                    'diferencia' => $totalDebe - $totalHaber,
                    'cantidad_asientos' => count($asientos)
                ]
            ];

            // Renderizar la vista Blade a HTML
            $html = View::make('reportes.pdfLibroDiario', compact('reporte'))->render();

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

            $pdfOutput = $mpdf->Output('libro-diario.pdf', 'S');

            return response($pdfOutput, 200)
                ->header('Content-Type', 'application/pdf')
                ->header('Content-Disposition', 'attachment; filename="libro-diario.pdf"');

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al generar el reporte libro diario: ' . $e->getMessage()
            ], 500);
        }
    }
}
