<?php

namespace App\Http\Controllers\Fiscalizacion;

use App\Models\Afip\DeclaracionesJuradasModelo;
use App\Models\EmpresaModelo;
use App\Models\Fiscalizacion\DeudaAporteEmpresa;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\View;

class DeudaAporteEmpresaController extends Controller
{
    public function index()
    {
        return DeudaAporteEmpresa::all();
    }

    public function buscarPorEmpresa($idempresa)
    {
        return DeudaAporteEmpresa::where('id_empresa', $idempresa)
            ->where('estado', 'Vigente')
            ->get();
    }

    public function getListDeudas(Request $request)
    {
        // Construir la consulta base con la relación empresa
        $query = DeudaAporteEmpresa::with('empresa')
            ->where('estado', 'Vigente');

        // Filtros por fechas de recálculo
        if ($request->filled('desde') && $request->filled('hasta')) {
            $query->whereDate('fecha_recalculo', '>=', $request->desde)
                ->whereDate('fecha_recalculo', '<=', $request->hasta);
        } elseif ($request->filled('desde')) {
            $query->whereDate('fecha_recalculo', '>=', $request->desde);
        } elseif ($request->filled('hasta')) {
            $query->whereDate('fecha_recalculo', '<=', $request->hasta);
        }

        // Filtro por año
        if ($request->filled('anio')) {
            $query->where('anio', '=', $request->anio);
        }

        // Filtro por mes
        if ($request->filled('mes')) {
            $query->where('mes', '=', $request->mes);
        }

        // Filtro por monto de deuda (desde/hasta)
        if ($request->filled('monto_desde') && $request->filled('monto_hasta')) {
            $query->whereBetween('monto_deuda', [$request->monto_desde, $request->monto_hasta]);
        } elseif ($request->filled('monto_desde')) {
            $query->where('monto_deuda', '>=', $request->monto_desde);
        } elseif ($request->filled('monto_hasta')) {
            $query->where('monto_deuda', '<=', $request->monto_hasta);
        }

        // Filtro por nombre de empresa (requiere join)
        if ($request->filled('nombre_empresa')) {
            $query->whereHas('empresa', function ($q) use ($request) {
                $q->where('razon_social', 'like', '%' . $request->nombre_empresa . '%');
            });
        }

        // Filtro por CUIT (requiere join)
        if ($request->filled('cuit')) {
            $query->whereHas('empresa', function ($q) use ($request) {
                $q->where('cuit', 'like', '%' . $request->cuit . '%');
            });
        }

        // Paginación
        $page = $request->input('page', 1);
        $perPage = $request->input('per_page', 10);
        $total = $query->count();

        $query->orderBy('fecha_recalculo', 'desc');

        $result = $query->skip(($page - 1) * $perPage)->take($perPage)->get();

        // Devolver la respuesta en formato JSON
        return response()->json([
            'data' => $result,
            'total' => $total
        ], 200);
    }

    // Detalle de deuda con intereses
    public function detalleDeuda(Request $request)
    {
        $periodo = $request->input('periodo'); // YYMM
        $cuit = $request->input('cuit');

        if (!$periodo || !$cuit) {
            return response()->json(['error' => 'Faltan parámetros: periodo y cuit'], 400);
        }

        // Traer todas las deudas del periodo
        $deudas = DB::table('tb_declaraciones_juradas as ddjj')
            ->selectRaw("
            ddjj.periodo,
            ddjj.cuil,
            ddjj.cuit,
            ddjj.remimpo AS importe_sueldo,
            ROUND(ddjj.remimpo * 0.03, 2) AS aporte,
            ROUND(ddjj.remimpo * 0.06, 2) AS contribucion,
            ddjj.fecpresent AS fecha_recalculo,
            DATE_ADD(ddjj.fecpresent, INTERVAL 30 DAY) AS fecha_vencimiento,
            emp.id_empresa,
            emp.razon_social,
            ROUND(ddjj.remimpo * 0.09, 2) AS monto_deuda,
            'APORTE' AS tipo_deuda,
            'Vigente' AS estado
        ")
            ->leftJoin('tb_transferencias as trf', function ($join) {
                $join->on('ddjj.cuil', '=', 'trf.cuitcont')
                    ->on('ddjj.periodo', '=', 'trf.periodo');
            })
            ->join('tb_empresa as emp', 'emp.cuit', '=', 'ddjj.cuit')
            ->whereNull('trf.id_transferencia')
            ->where('ddjj.periodo', $periodo)
            ->where('emp.cuit', $cuit)
            ->orderBy('ddjj.fecpresent')
            ->get();

        if ($deudas->isEmpty()) {
            return response()->json(['error' => 'No se encontraron deudas para el periodo y CUIT especificados'], 404);
        }

        $hoy = now()->toDateString();

        // Calcular intereses y total de cada deuda
        $deudas->transform(function ($deuda) use ($hoy) {

            $tasas = DB::table('tb_fisca_tasas_interes')
                ->where('articulo_resolucion', 'Artículo 1°')
                ->where(function ($q) use ($deuda, $hoy) {
                    $q->where('vigencia_inicio', '<=', $hoy)
                        ->where(function ($q2) use ($deuda) {
                            $q2->whereNull('vigencia_fin')
                                ->orWhere('vigencia_fin', '>=', $deuda->fecha_vencimiento);
                        });
                })
                ->orderBy('vigencia_inicio')
                ->get();

            $interesCalculado = 0;

            foreach ($tasas as $tasa) {
                $inicio = \Carbon\Carbon::parse($deuda->fecha_vencimiento)->greaterThan(\Carbon\Carbon::parse($tasa->vigencia_inicio))
                    ? \Carbon\Carbon::parse($deuda->fecha_vencimiento)
                    : \Carbon\Carbon::parse($tasa->vigencia_inicio);

                $fin = \Carbon\Carbon::parse($tasa->vigencia_fin ?? $hoy)->lessThan(\Carbon\Carbon::parse($hoy))
                    ? \Carbon\Carbon::parse($tasa->vigencia_fin)
                    : \Carbon\Carbon::parse($hoy);

                $dias = $inicio->diffInDays($fin) + 1;

                $interesCalculado += $deuda->monto_deuda * $tasa->interes_diario * $dias;
            }

            $deuda->intereses = round($interesCalculado, 2);
            $deuda->monto_total = round($deuda->monto_deuda + $interesCalculado, 2);

            return $deuda;
        });

        return response()->json($deudas, 200);
    }

    public function pdfDeudaEmpresa(Request $request)
    {
        $empresa = EmpresaModelo::with(['localidad'])
            ->where('cuit', $request->empresa)
            ->first();

        $detalle = DeclaracionesJuradasModelo::where('cuit', [$request->empresa])
        ->orderByDesc('periodo')
        ->get();


        $datos = DB::select("SELECT concat(f.anio,f.mes) as periodo,f.importe_sueldo,f.contribucion,f.monto_deuda,
                            (SELECT COUNT(DISTINCT cuil) FROM tb_declaraciones_juradas where cuit = e.cuit and periodo = concat(substr(f.anio,3,4),f.mes)) as cant_empleados
                            FROM tb_fisca_deudas_aportes_empresa f INNER JOIN tb_empresa e ON f.id_empresa  = e.id_empresa
                            WHERE e.cuit = ? ", [$request->empresa]);

        $html = View::make('reportes.pdfdeudaempresa', compact('datos', 'empresa'))->render();
        $pagedetalle = View::make('reportes.pdfdetallecuilesdeuda', compact('detalle'))->render();
        $mpdf = new \Mpdf\Mpdf([
            'default_font' => 'centurygothic',
            'format' => 'A4',
            'margin_top' => 5,     // Margen superior en milímetros
            'margin_bottom' => 0,  // Margen inferior en milímetros
            'margin_left' => 6,    // Margen izquierdo en milímetros
            'margin_right' => 6    // Margen derecho en milímetros
        ]);

        $mpdf->fontdata['centurygothic'] = [
            'R' => 'resources/fonts/Quicksand-Regular.ttf',   // Fuente normal
            'B' => 'resources/fonts/Quicksand-Bold.ttf',     // Negrita
            'I' => 'resources/fonts/Quicksand-Light.ttf',   // Cursiva
            'BI' => 'resources/fonts/Quicksand-SemiBold.ttf' // Negrita + Cursiva
        ];

        // $mpdf->SetWatermarkText($convenio == 1 ? 'CONVENIO COLECTIVO' : '', 0.05);

        $mpdf->showWatermarkText = true;

        $mpdf->WriteHTML($html);//'A4-L'
        $mpdf->AddPage();
        $mpdf->WriteHTML($pagedetalle);
        return response($mpdf->Output('recetario.pdf', 'S'), 200)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'attachment; filename="medicacion.pdf"');
    }
}
