<?php

namespace App\Http\Controllers\liquidaciones\repository;

use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Models\liquidaciones\LiqMedicamentosEntity;
use Illuminate\Support\Facades\DB;

class LiqMedicamentosRepository
{

    public function save($params)
    {
        $user = Auth::user();
        $fechaActual = Carbon::now('America/Argentina/Buenos_Aires');

        $model = LiqMedicamentosEntity::create([
            'id_factura' => $params->id_factura,
            'id_afiliado' => $params->id_afiliado,
            'edad_afiliado' => $params->edad_afiliado,
            'id_cobertura' => $params->id_cobertura,
            'id_tipo_iva' => $params->id_tipo_iva,
            'cod_profesional' => $params->cod_profesional,
            'cod_provincia' => $params->cod_provincia,
            'diagnostico' => $params->diagnostico,
            'observaciones_debito' => $params->observacion_debito,
            'observaciones' => $params->observaciones,
            'fecha_venta' => $params->fecha_venta,
            'fecha_prescripcion' => $params->fecha_prescripcion,
            'referencia' => $params->referencia,
            'fecha_registra' => $fechaActual,
            'cod_usuario' => $user->cod_usuario,
            'total_facturado' => $params->total_facturado,
            'total_aprobado' => $params->total_aprobado,
            'total_debitado' => $params->total_debitado
        ]);

        return $model;
    }

    public function saveId($id, $params)
    {
        $fechaActual = Carbon::now('America/Argentina/Buenos_Aires');
        $liq = LiqMedicamentosEntity::find($id);
        $liq->id_factura = $params->id_factura;
        $liq->id_afiliado = $params->id_afiliado;
        $liq->edad_afiliado = $params->edad_afiliado;
        $liq->id_cobertura = $params->id_cobertura;
        $liq->id_tipo_iva = $params->id_tipo_iva;
        $liq->cod_profesional = $params->cod_profesional;
        $liq->cod_provincia = $params->cod_provincia;
        $liq->diagnostico = $params->diagnostico;
        $liq->observaciones_debito = $params->observacion_debito;
        $liq->observaciones = $params->observaciones;
        $liq->fecha_venta = $params->fecha_venta;
        $liq->fecha_prescripcion = $params->fecha_prescripcion;
        $liq->referencia = $params->referencia;
        $liq->total_facturado = $params->total_facturado;
        $liq->total_aprobado = $params->total_aprobado;
        $liq->total_debitado = $params->total_debitado;
        $liq->fecha_actualiza = $fechaActual;
        $liq->save();
        return $liq;
    }

    public function deleteId($id)
    {
        return LiqMedicamentosEntity::find($id)->delete();
    }

    public function findByMedicamentosCabeceraId($id)
    {
        return DB::select("SELECT id_liquidacion,fecha_prestacion,fecha_prescripcion,referencia,id_factura,id_afiliado,
        dni_afiliado,afiliado,edad_afiliado,id_cobertura,id_tipo_iva,dni_medico,medico,cod_profesional,cod_provincia,diagnostico,observaciones,
        observaciones_debito,total_facturado,total_aprobado,total_debitado FROM vw_matriz_liquidaciones_medicamentos WHERE id_liquidacion = ?", [$id]);
    }

    public function findByMedicamentosDetalleId($id)
    {
        return DB::select("SELECT * FROM vw_detalle_medicamentos WHERE id_liquidacion = ?", [$id]);
    }

    public function findByLiquidacionAndDetalleId($id)
    {
        DB::delete("DELETE FROM tb_liquidaciones_detalle_medicamentos WHERE id_liquidacion = ?", [$id]);
        return DB::delete("DELETE FROM tb_liquidaciones_medicamentos WHERE id_liquidacion = ?", [$id]);
    }

    public function findByFacturaId($id)
    {
        return DB::selectOne("SELECT * FROM vw_matriz_facturas_prestador WHERE id_factura = ?", [$id]);
    }

    public function findByDetalleFacturaId($id_factura, $tipo)
    {
        return DB::select("SELECT  codigo_practica,practica,monto_facturado,monto_aprobado,monto_debitado,debita_iva,coseguro,debita_coseguro,motivo_debito,observacion_debito,costo_practica,
                            afiliado,edad_afiliado,dni_afiliado,tipo,id_factura,observacion_debito
                            FROM vw_detalle_liquidaciones WHERE id_factura =  $id_factura  AND tipo =  '$tipo'
                            UNION ALL
                            SELECT id_medicamento as codigo_practica, medicamento as practica,monto_facturado,0 as monto_aprobado,0 as monto_debitado,
                            debita_iva,0 as coseguro,0 as debita_coseguro,motivo_debito, '' as  observacion_debito,precio_unitario as costo_practica,
                            '' as afiliado,'' as edad_afiliado,'' as dni_afiliado,tipo, id_factura,'' as observacion_debito
                            FROM vw_detalle_medicamentos WHERE id_factura =   $id_factura  AND tipo =  '$tipo'
                            ORDER BY motivo_debito ASC");
    }
}
