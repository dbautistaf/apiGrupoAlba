<?php

namespace App\Http\Controllers\Dashboard\Repository;

use Illuminate\Support\Facades\DB;

class DashboardRepository
{
    public function getDashboardTotals($fechaDesde = null, $fechaHasta = null)
    {
        return [
            // ========== DATOS ORIGINALES ==========
            'total_facturacion' => $this->countWithFilter('tb_facturacion_datos', 'fecha_comprobante', $fechaDesde, $fechaHasta),
            'total_liquidaciones' => $this->countWithFilter('tb_liquidaciones', 'fecha_registra', $fechaDesde, $fechaHasta),
            'total_autorizaciones' => $this->countWithFilter('tb_prestaciones_medicas', 'fecha_registra', $fechaDesde, $fechaHasta),
            'total_internaciones' => $this->countWithFilter('tb_internaciones', 'fecha_internacion', $fechaDesde, $fechaHasta),
            'total_historias_clinicas' => $this->countWithFilter('tb_historia_clinica', 'fecha_registra', $fechaDesde, $fechaHasta),
            'total_empresas' => $this->countWithFilter('tb_empresa', 'fecha_alta', $fechaDesde, $fechaHasta),
            'total_intimaciones' => $this->countWithFilter('tb_fisca_seguimiento_intimacion', 'fecha_inicio_gestion', $fechaDesde, $fechaHasta),
            'total_cobranzas' => $this->countWithFilter('tb_fisca_cobranzas', 'fecha_creacion', $fechaDesde, $fechaHasta),
            'total_acuerdos_pago' => $this->countWithFilter('tb_fisca_acuerdo_pago', 'fecha_creacion', $fechaDesde, $fechaHasta),
            'total_escolaridad' => $this->countWithFilter('tb_escolaridad', 'fecha_presentacion', $fechaDesde, $fechaHasta),
            'total_discapacidad_legajos' => $this->countWithFilter('tb_discapacidad_legajos', 'fecha_certificado', $fechaDesde, $fechaHasta),
            'total_discapacidad' => $this->countWithFilter('tb_discapacidad', 'fecha_registra', $fechaDesde, $fechaHasta),
            'total_derivaciones' => $this->countWithFilter('tb_derivacion', 'fecha_solicitud', $fechaDesde, $fechaHasta),
            'total_internacion_domiciliaria' => $this->countWithFilter('tb_internaciones_domiciliaria', 'fecha_solicitud', $fechaDesde, $fechaHasta),
            'total_protesis' => $this->countWithFilter('tb_protesis', 'fecha_emision', $fechaDesde, $fechaHasta),
            'total_altas_monotributo' => $this->countWithFilter('tb_altas_monotributo', 'fecha_registra', $fechaDesde, $fechaHasta),
            'total_altas_reg_gral' => $this->countWithFilter('tb_altas_regimen_general', 'fecha_registra', $fechaDesde, $fechaHasta),
            'total_bajas_reg_gral' => $this->countWithFilter('tb_bajas_regimen_general', 'fecha_registra', $fechaDesde, $fechaHasta),
            'total_bajas_monotributo' => $this->countWithFilter('tb_bajas_monotributo', 'fecha_registra', $fechaDesde, $fechaHasta),
            'total_ordenes_pago' => $this->countWithFilter('tb_tes_orden_pago', 'fecha_emision', $fechaDesde, $fechaHasta),
            'total_padron_comercial' => $this->countWithFilter('tb_padron_comercial', 'fe_alta', $fechaDesde, $fechaHasta),
            'total_coseguros' => $this->countWithFilter('tb_matriz_coseguros', 'fecha_carga', $fechaDesde, $fechaHasta),
            'total_recaudacion' => $this->countWithFilter('tb_liquidaciones_obras', 'fecha_proceso', $fechaDesde, $fechaHasta),
            
            // ========== NUEVOS DATOS ==========
            
            // AFILIACIONES
            'beneficiarios_mayoria_edad' => $this->getBeneficiariosMayoriaEdad(),
            'afiliados_escolaridad_activa' => $this->getAfiliadosEscolaridadActiva(),
            
            // AUDITORÍAS
            'total_auditorias_terreno' => $this->countWithFilter('tb_internaciones_auditadas', 'fecha_autoriza', $fechaDesde, $fechaHasta),
            'total_auditorias_tecnicas' => $this->countWithFilter('tb_facturacion_auditar', 'fecha_audita', $fechaDesde, $fechaHasta),
            
            // FACTURACIÓN
            'facturas_por_tipo' => $this->getFacturasPorTipo($fechaDesde, $fechaHasta),
            
            // REINTEGROS
            'total_reintegros' => $this->countWithFilter('tb_reintegros', 'fecha_solicitud', $fechaDesde, $fechaHasta),
            'total_reintegros_pendientes' => $this->getReintegrosPorEstado('PENDIENTE', $fechaDesde, $fechaHasta),
            'total_reintegros_autorizados' => $this->getReintegrosPorEstado('AUTORIZADO', $fechaDesde, $fechaHasta),
            
            // RECETAS
            'total_recetas' => $this->countWithFilter('tb_recetas', 'fecha_receta', $fechaDesde, $fechaHasta),
            
            // RECETARIOS
            'total_recetarios' => $this->countWithFilter('tb_recetarios_medicacion', 'fecha_registra', $fechaDesde, $fechaHasta),
            
            // FISCALIZACIÓN
            'ingresos_acuerdos_pago' => $this->getIngresosAcuerdosPago($fechaDesde, $fechaHasta),
        ];
    }

    /**
     * Cuenta registros con filtro de fecha opcional
     */
    private function countWithFilter($table, $dateColumn, $fechaDesde = null, $fechaHasta = null)
    {
        $query = DB::table($table);
        
        if ($fechaDesde && $fechaHasta) {
            $query->whereBetween($dateColumn, [$fechaDesde, $fechaHasta]);
        }
        
        return $query->count();
    }
    
    /**
     * Beneficiarios próximos a cumplir mayoría de edad (18 a 20 años)
     */
    private function getBeneficiariosMayoriaEdad()
    {
        return DB::table('tb_padron')
            ->whereRaw('TIMESTAMPDIFF(YEAR, fe_nac, CURDATE()) BETWEEN 18 AND 20')
            ->where('activo', 1)
            ->count();
    }
    
    /**
     * Afiliados con escolaridad activa (21 a 25 años cursando estudios regulares)
     */
    private function getAfiliadosEscolaridadActiva()
    {
        return DB::table('tb_escolaridad as e')
            ->join('tb_padron as p', 'e.id_padron', '=', 'p.id')
            ->whereRaw('TIMESTAMPDIFF(YEAR, p.fe_nac, CURDATE()) BETWEEN 21 AND 25')
            ->where('p.activo', 1)
            ->where('e.fecha_vencimiento', '>=', DB::raw('CURDATE()'))
            ->count();
    }
    
    /**
     * Obtiene facturas agrupadas por tipo
     */
    private function getFacturasPorTipo($fechaDesde = null, $fechaHasta = null)
    {
        $query = DB::table('tb_facturacion_datos as fd')
            ->join('tb_facturacion_tipo as ft', 'fd.id_tipo_factura', '=', 'ft.id_tipo_factura')
            ->select('ft.descripcion as tipo', DB::raw('COUNT(*) as total'))
            ->groupBy('ft.id_tipo_factura', 'ft.descripcion');
        
        if ($fechaDesde && $fechaHasta) {
            $query->whereBetween('fd.fecha_comprobante', [$fechaDesde, $fechaHasta]);
        }
        
        return $query->get();
    }
    
    /**
     * Obtiene reintegros por estado
     */
    private function getReintegrosPorEstado($estadoNombre, $fechaDesde = null, $fechaHasta = null)
{
    $query = DB::table('tb_reintegros as r')
        ->join('tb_estado_autorizacion as ea', 'r.id_estado_autorizacion', '=', 'ea.id_estado_autorizacion')
        ->where('ea.estado_autorizacion', 'LIKE', "%{$estadoNombre}%");
    
    if ($fechaDesde && $fechaHasta) {
        $query->whereBetween('r.fecha_solicitud', [$fechaDesde, $fechaHasta]);
    }
    
    return $query->count();
}
    
    /**
     * Obtiene suma de ingresos por acuerdos de pago
     */
    private function getIngresosAcuerdosPago($fechaDesde = null, $fechaHasta = null)
    {
        $query = DB::table('tb_fisca_acuerdo_pago');
        
        if ($fechaDesde && $fechaHasta) {
            $query->whereBetween('fecha_creacion', [$fechaDesde, $fechaHasta]);
        }
        
        return $query->sum('importe_total') ?? 0;
    }
}