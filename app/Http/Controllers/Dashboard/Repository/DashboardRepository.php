<?php

namespace App\Http\Controllers\Dashboard\Repository;

use Illuminate\Support\Facades\DB;

class DashboardRepository
{
    public function getDashboardTotals($fechaDesde = null, $fechaHasta = null)
    {
        return [
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
}