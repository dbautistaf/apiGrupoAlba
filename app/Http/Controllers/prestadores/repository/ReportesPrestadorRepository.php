<?php

namespace App\Http\Controllers\prestadores\repository;

use App\Models\Prestadores\PrestadorEntity;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class ReportesPrestadorRepository
{
    public function getPrestadoresConFacturasImpagas($fechaInicio = null, $fechaFin = null): Collection
    {
        $query = PrestadorEntity::with([
            'facturas' => function ($q) use ($fechaInicio, $fechaFin) {
                $q->with(['filial', 'tipoFactura'])
                    ->where('estado_pago', 0);

                if ($fechaInicio) {
                    $q->whereDate('fecha_comprobante', '>=', $fechaInicio);
                }

                if ($fechaFin) {
                    $q->whereDate('fecha_comprobante', '<=', $fechaFin);
                }
            }
        ])
            ->whereHas('facturas', function ($q) use ($fechaInicio, $fechaFin) {
                $q->where('estado_pago', 0);

                if ($fechaInicio) {
                    $q->whereDate('fecha_comprobante', '>=', $fechaInicio);
                }

                if ($fechaFin) {
                    $q->whereDate('fecha_comprobante', '<=', $fechaFin);
                }
            });

        return $query->get();
    }

    public function getResumenFacturasImpagas($fechaInicio = null, $fechaFin = null)
    {
        $query = DB::table('tb_prestador as p')
            ->join('tb_facturacion_datos as f', 'p.cod_prestador', '=', 'f.id_prestador')
            ->select([
                'p.cod_prestador as prestador_id',
                'p.razon_social as nombre_completo',
                'p.razon_social',
                'p.cuit as numero_documento',
                'p.email',
                'p.celular as telefono',
                DB::raw('COUNT(f.id_factura) as total_facturas_impagas'),
                DB::raw('SUM(f.total_neto) as monto_total_impago')
            ])
            ->where(function ($q) {
                $q->where('f.estado_pago', 0);
            });

        if ($fechaInicio) {
            $query->whereDate('f.fecha_comprobante', '>=', $fechaInicio);
        }

        if ($fechaFin) {
            $query->whereDate('f.fecha_comprobante', '<=', $fechaFin);
        }

        return $query->groupBy([
            'p.cod_prestador',
            'p.razon_social',
            'p.cuit',
            'p.email',
            'p.celular'
        ])->get();
    }
}
