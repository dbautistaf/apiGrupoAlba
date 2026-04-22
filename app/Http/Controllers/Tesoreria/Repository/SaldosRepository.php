<?php

namespace App\Http\Controllers\Tesoreria\Repository;

use App\Models\facturacion\FacturacionDatosEntity;
use App\Models\Prestadores\PrestadorEntity;
use App\Models\proveedor\MatrizProveedoresEntity;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class SaldosRepository
{
    private $user;
    private $fechaActual;

    public function __construct()
    {
        $this->user = Auth::user();
        $this->fechaActual = Carbon::now('America/Argentina/Buenos_Aires');
    }

    /**
     * Obtiene proveedores y prestadores con sus deudas pendientes
     * Incluye cálculo de deuda considerando OPAs y pagos asociados.
     * 
     * @param array $filtros Filtros para la búsqueda (cuit, razon_social, tipo)
     * @param int $perPage Número de elementos por página
     * @return LengthAwarePaginator
     */
    public function getProveedoresPrestadoresConDeudas($filtros = [], $perPage = 15): LengthAwarePaginator
    {
        // Query para proveedores con deudas
        $proveedoresQuery = DB::table('tb_facturacion_datos as f')
            ->join('tb_proveedor as p', 'f.id_proveedor', '=', 'p.cod_proveedor')
            ->leftJoin('tb_tes_opa_factura as of', 'f.id_factura', '=', 'of.id_factura')
            ->leftJoin('tb_tes_pago as tp', 'of.id_orden_pago', '=', 'tp.id_orden_pago')
            ->select([
                'p.cod_proveedor as id',
                'p.cuit',
                'p.razon_social',
                DB::raw("'PROVEEDOR' as tipo"),
                DB::raw('COUNT(DISTINCT f.id_factura) as cantidad_facturas_pendientes'),
                DB::raw('SUM(f.total_neto - IFNULL(tp.monto_total_pagado + tp.monto_total_retenido, 0)) as total_deuda')
            ])
            ->where('f.id_estado_pago', '!=', 3) // No pagadas
            ->whereNotNull('f.id_proveedor')
            ->groupBy('p.cod_proveedor', 'p.cuit', 'p.razon_social');

        // Query para prestadores con deudas
        $prestadoresQuery = DB::table('tb_facturacion_datos as f')
            ->join('tb_prestador as pr', 'f.id_prestador', '=', 'pr.cod_prestador')
            ->leftJoin('tb_tes_opa_factura as of', 'f.id_factura', '=', 'of.id_factura')
            ->leftJoin('tb_tes_pago as tp', 'of.id_orden_pago', '=', 'tp.id_orden_pago')
            ->select([
                'pr.cod_prestador as id',
                'pr.cuit',
                'pr.razon_social',
                DB::raw("'PRESTADOR' as tipo"),
                DB::raw('COUNT(DISTINCT f.id_factura) as cantidad_facturas_pendientes'),
                DB::raw('SUM(f.total_neto - IFNULL(tp.monto_total_pagado + tp.monto_total_retenido, 0)) as total_deuda')
            ])
            ->where('f.id_estado_pago', '!=', 3) // No pagadas
            ->whereNotNull('f.id_prestador')
            ->groupBy('pr.cod_prestador', 'pr.cuit', 'pr.razon_social');

        // Aplicar filtros si existen
        if (!empty($filtros['cuit'])) {
            $proveedoresQuery->where('p.cuit', 'LIKE', '%' . $filtros['cuit'] . '%');
            $prestadoresQuery->where('pr.cuit', 'LIKE', '%' . $filtros['cuit'] . '%');
        }

        if (!empty($filtros['razon_social'])) {
            $proveedoresQuery->where('p.razon_social', 'LIKE', '%' . $filtros['razon_social'] . '%');
            $prestadoresQuery->where('pr.razon_social', 'LIKE', '%' . $filtros['razon_social'] . '%');
        }

        // Union de las queries según el tipo solicitado
        if (!empty($filtros['tipo'])) {
            if (strtoupper($filtros['tipo']) === 'PROVEEDOR') {
                $query = $proveedoresQuery;
            } elseif (strtoupper($filtros['tipo']) === 'PRESTADOR') {
                $query = $prestadoresQuery;
            } else {
                $query = $proveedoresQuery->union($prestadoresQuery);
            }
        } else {
            $query = $proveedoresQuery->union($prestadoresQuery);
        }

        // Ordenar por total de deuda descendente
        $query->orderBy('total_deuda', 'DESC');

        // Ejecutar la paginación
        $results = DB::table(DB::raw("({$query->toSql()}) as resultados"))
            ->mergeBindings($query)
            ->paginate($perPage);

        return $results;
    }

    /**
     * Obtiene el detalle de facturas pendientes de un proveedor o prestador específico
     * 
     * @param string $tipo 'PROVEEDOR' o 'PRESTADOR'  
     * @param int $id ID del proveedor o prestador
     * @param int $perPage Número de elementos por página
     * @return LengthAwarePaginator
     */
    public function getDetalleFacturasPendientes($tipo, $id, $perPage = 15): LengthAwarePaginator
    {
        $query = DB::table('tb_facturacion_datos as f')
            ->leftJoin('tb_facturacion_estado_pago as ep', 'f.id_estado_pago', '=', 'ep.id_estado_pago')
            ->leftJoin('tb_facturacion_tipo_comprobantes as tc', 'f.id_tipo_comprobante', '=', 'tc.id_tipo_comprobante')
            ->select([
                'f.id_factura',
                'f.fecha_comprobante',
                'f.fecha_vencimiento',
                'f.numero',
                'f.sucursal',
                'f.tipo_letra',
                'f.total_neto',
                'f.periodo',
                'ep.descripcion as estado_pago',
                'tc.descripcion as tipo_comprobante',
                DB::raw('DATEDIFF(CURRENT_DATE, f.fecha_vencimiento) as dias_vencimiento')
            ])
            ->where('f.id_estado_pago', '!=', 3); // No pagadas

        if (strtoupper($tipo) === 'PROVEEDOR') {
            $query->where('f.id_proveedor', $id);
        } elseif (strtoupper($tipo) === 'PRESTADOR') {
            $query->where('f.id_prestador', $id);
        }

        $query->orderBy('f.fecha_vencimiento', 'ASC');

        return $query->paginate($perPage);
    }

    /**
     * Obtiene resumen de deudas por tipo (PROVEEDOR/PRESTADOR)
     * 
     * @return array
     */
    public function getResumenDeudasPorTipo()
    {
        $resumen = DB::select("
            SELECT 
                'PROVEEDOR' as tipo,
                COUNT(DISTINCT p.cod_provedor) as cantidad_acreedores,
                COUNT(f.id_factura) as cantidad_facturas,
                SUM(f.total_neto) as total_deuda
            FROM tb_facturacion_datos f
            JOIN tb_provedores_discapacidad p ON f.id_proveedor = p.cod_provedor
            WHERE f.id_estado_pago != 3
            
            UNION ALL
            
            SELECT 
                'PRESTADOR' as tipo,
                COUNT(DISTINCT pr.cod_prestador) as cantidad_acreedores,
                COUNT(f.id_factura) as cantidad_facturas,
                SUM(f.total_neto) as total_deuda
            FROM tb_facturacion_datos f
            JOIN tb_prestador pr ON f.id_prestador = pr.cod_prestador
            WHERE f.id_estado_pago != 3
        ");

        return $resumen;
    }
}
