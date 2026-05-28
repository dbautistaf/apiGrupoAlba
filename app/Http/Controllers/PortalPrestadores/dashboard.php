<?php

namespace App\Http\Controllers\PortalPrestadores;

use App\Http\Controllers\Controller;
use App\Models\Afip\TransferenciasModelo;
use App\Models\BonoClinicoEntity;
use App\Models\Internaciones\InternacionesEntity;
use App\Models\PrestacionesPracticaLaboratorioEntity;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as RoutingController;
use Illuminate\Support\Facades\DB;

class dashboard extends RoutingController
{
    //
    public function getDashboard(Request $request)
    {


        $anio = $request->anio ?? Carbon::now()->year;

        $bonos = $this->aplicarFiltros(BonoClinicoEntity::query(), $request, 'fecha_registra')->count();

        $autorizaciones = $this->aplicarFiltros(PrestacionesPracticaLaboratorioEntity::query(), $request, 'fecha_registra')->count();

        $internacion = $this->aplicarFiltros(InternacionesEntity::query(), $request, 'fecha_internacion')->count();

        $importeBonos = $this->aplicarFiltros(
            BonoClinicoEntity::query()->select(DB::raw('SUM(costo_bono) as total')),
            $request,
            'fecha_registra'
        )->value('total') ?? 0;

        $importeAutorizaciones = $this->aplicarFiltros(
            PrestacionesPracticaLaboratorioEntity::query()->select(DB::raw('SUM(monto_pagar) as total')),
            $request,
            'fecha_registra'
        )->value('total') ?? 0;

        $bonosMensual = $this->aplicarFiltros(
            BonoClinicoEntity::query()
                ->select(
                    DB::raw('MONTH(fecha_registra) as mes'),
                    DB::raw('SUM(costo_bono) as total')
                )
                ->whereYear('fecha_registra', $anio)
                ->groupBy(DB::raw('MONTH(fecha_registra)')),
            $request,
            'fecha_registra'
        );

        $autMensual = $this->aplicarFiltros(
            PrestacionesPracticaLaboratorioEntity::query()
                ->select(
                    DB::raw('MONTH(fecha_registra) as mes'),
                    DB::raw('SUM(monto_pagar) as total')
                )
                ->whereYear('fecha_registra', $anio)
                ->groupBy(DB::raw('MONTH(fecha_registra)')),
            $request,
            'fecha_registra'
        );
        $union = $bonosMensual->unionAll($autMensual);

        $importe_mensual = DB::table(DB::raw("({$union->toSql()}) as t"))
            ->mergeBindings($union->getQuery())
            ->select(
                'mes',
                DB::raw('SUM(total) as total')
            )
            ->groupBy('mes')
            ->orderBy('mes')
            ->get()
            ->map(function ($item) {
                $item->total_formateado = number_format($item->total, 2, ',', '.');
                return $item;
            });
            
            
       $importesFamiliaBonos = $this->aplicarFiltros(
            BonoClinicoEntity::query()->selectRaw("
                SUM(CASE 
                    WHEN tb_padron.id_parentesco = '00' THEN costo_bono 
                    ELSE 0 
                END) as total_titular,

                SUM(CASE 
                    WHEN tb_padron.id_parentesco != '00' THEN costo_bono 
                    ELSE 0 
                END) as total_familiar
            ")
            ->join('tb_padron', 'tb_padron.dni', '=', 'tb_bonos_medicos.dni_afiliado'),
            $request,
            'fecha_registra'
        )->first();
        
        $importesFamiliaAuto = $this->aplicarFiltros(
            PrestacionesPracticaLaboratorioEntity::query()->selectRaw("
                SUM(CASE 
                    WHEN tb_padron.id_parentesco = '00' THEN monto_pagar 
                    ELSE 0 
                END) as total_titular,

                SUM(CASE 
                    WHEN tb_padron.id_parentesco != '00' THEN monto_pagar 
                    ELSE 0 
                END) as total_familiar
            ")
            ->join('tb_padron', 'tb_padron.dni', '=', 'tb_prestaciones_medicas.dni_afiliado'),
            $request,
            'fecha_registra'
        )->first();

        $totalImporte = number_format(($importeAutorizaciones + $importeBonos), 2, ',', '.');
        $totalBonos = number_format(($importeBonos), 2, ',', '.');
        $totalAuto = number_format(($importeAutorizaciones), 2, ',', '.');
        
        $totalTitular = number_format(($importesFamiliaAuto->total_titular + $importesFamiliaBonos->total_titular), 2, ',', '.');
        $totalfamiliar = number_format(($importesFamiliaAuto->total_familiar + $importesFamiliaBonos->total_familiar), 2, ',', '.');
        
        return response()->json([
            'success' => true,
            'data' => [
                'bonos' => $bonos,
                'autorizaciones' => $autorizaciones,
                'internacion' => $internacion,
                'importe' => $totalImporte,
                'importe_bonos' => $totalBonos,
                'importe_auto' => $totalAuto,
                'importe_anual' => $importe_mensual,
                'titular' => $totalTitular,
                'familia' =>$totalfamiliar
            ]
        ]);
    }

    private function aplicarFiltros($query, $request, $campoFecha, $relacion = 'afiliado')
    {
        return $query
            ->when($request->id_locatario, function ($q) use ($request, $relacion) {
                $q->whereHas($relacion, function ($sub) use ($request) {
                    $sub->where('id_locatario', $request->id_locatario);
                });
            })
            ->when($request->cuil, function ($q) use ($request, $relacion) {
                $q->whereHas($relacion, function ($sub) use ($request) {
                    $sub->where('cuil_benef', $request->cuil);
                });
            })
            ->when($request->desde && $request->hasta, function ($q) use ($request, $campoFecha) {
                $q->whereBetween($campoFecha, [$request->desde, $request->hasta]);
            });
    }
}
