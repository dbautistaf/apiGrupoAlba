<?php

namespace App\Http\Controllers\Discapacidad\Repository;

use App\Models\Discapacidad\DiscapacidadTesoreriaEntity;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class RendicionFondosRepository
{

    public function save($params)
    {
        $fechaActual = Carbon::now('America/Argentina/Buenos_Aires');
        $user = Auth::user();
        return DiscapacidadTesoreriaEntity::create([
            'id_discapacidad' => $params->id_discapacidad,
            'cuit_prestador' => $params->cuit_prestador,
            'cbu' => $params->cbu,
            'orden_pago_1' => $params->orden_pago_1,
            'orden_pago_2' => $params->orden_pago_2,
            'fecha_transferencia_1' => $params->fecha_transferencia_1,
            'fecha_transferencia_2' => $params->fecha_transferencia_2,
            'cheque' => $params->cheque,
            'importe_transferido' => $params->importe_transferido,
            'retencion_ganancias' => $params->retencion_ganancias,
            'retencion_ingresos_brutos' => $params->retencion_ingresos_brutos,
            'otras_retenciones' => $params->otras_retenciones,
            'importe_aplicado_sss' => $params->importe_aplicado_sss,
            'fondos_propios_cuenta_discapacidad' => $params->fondos_propios_cuenta_discapacidad,
            'fondos_propios_otra_cuenta' => $params->fondos_propios_otra_cuenta,
            'numero_recibo' => $params->numero_recibo,
            'importe_reversion' => $params->importe_reversion,
            'importe_devuelto_cuenta_sss' => $params->importe_devuelto_cuenta_sss,
            'saldo_no_aplicado' => $params->saldo_no_aplicado,
            'recupero_fondos_propios' => $params->recupero_fondos_propios,
            'diferencia' => $params->diferencia,
            'observaciones' => $params->observaciones,
            'fecha_proceso' => $fechaActual,
            'id_usuario' => $user->cod_usuario
        ]);
    }

    public function saveId($params)
    {
        $tes = DiscapacidadTesoreriaEntity::find($params->id_discapacidad_tesoreria);
        $tes->id_discapacidad = $params->id_discapacidad;
        $tes->cuit_prestador = $params->cuit_prestador;
        $tes->cbu = $params->cbu;
        $tes->orden_pago_1 = $params->orden_pago_1;
        $tes->orden_pago_2 = $params->orden_pago_2;
        $tes->fecha_transferencia_1 = $params->fecha_transferencia_1;
        $tes->fecha_transferencia_2 = $params->fecha_transferencia_2;
        $tes->cheque = $params->cheque;
        $tes->importe_transferido = $params->importe_transferido;
        $tes->retencion_ganancias = $params->retencion_ganancias;
        $tes->retencion_ingresos_brutos = $params->retencion_ingresos_brutos;
        $tes->otras_retenciones = $params->otras_retenciones;
        $tes->importe_aplicado_sss = $params->importe_aplicado_sss;
        $tes->fondos_propios_cuenta_discapacidad = $params->fondos_propios_cuenta_discapacidad;
        $tes->fondos_propios_otra_cuenta = $params->fondos_propios_otra_cuenta;
        $tes->numero_recibo = $params->numero_recibo;
        $tes->importe_reversion = $params->importe_reversion;
        $tes->importe_devuelto_cuenta_sss = $params->importe_devuelto_cuenta_sss;
        $tes->saldo_no_aplicado = $params->saldo_no_aplicado;
        $tes->recupero_fondos_propios = $params->recupero_fondos_propios;
        $tes->diferencia = $params->diferencia;
        $tes->observaciones = $params->observaciones;
        $tes->update();
    }

    public function findByIdDiscapacidad($id)
    {
        return DiscapacidadTesoreriaEntity::where('id_discapacidad', $id)->first();
    }

    public function findTopByListperiodo($desde, $hasta, $top, $estado)
    {
        $query = "";
        if ($estado == '1') {
            $query = " AND id_discapacidad_tesoreria IS NOT NULL ";
        } else if ($estado == '2') {
            $query = " AND id_discapacidad_tesoreria IS NULL ";
        }
        return DB::select(
            "SELECT * FROM vw_discapacidad_tesoreria WHERE periodo_prestacion BETWEEN ? AND ?  $query LIMIT ? ",
            [$desde, $hasta, $top]
        );

    }

    public function findByCountRegistrosCargados($desde, $hasta)
    {
        return DB::table('vw_discapacidad_tesoreria')
            ->whereBetween('periodo_prestacion', [$desde, $hasta])
            ->whereNotNull('id_discapacidad_tesoreria')
            ->count();
    }

    public function findTopByListCuitPrest($desde, $hasta, $cuit, $top, $estado)
    {
        $query = "";
        if ($estado == '1') {
            $query = " AND id_discapacidad_tesoreria IS NOT NULL ";
        } else if ($estado == '2') {
            $query = " AND id_discapacidad_tesoreria IS NULL ";
        }
        return DB::select("SELECT * FROM vw_discapacidad_tesoreria WHERE periodo_prestacion BETWEEN ? AND ?
            AND cuil_prestador LIKE ? $query
         LIMIT ? ", [$desde, $hasta, '%' . $cuit . '%', $top]);
    }

    public function findTopByListNumfact($desde, $hasta, $num_factura, $top, $estado)
    {
        $query = "";
        if ($estado == '1') {
            $query = " AND id_discapacidad_tesoreria IS NOT NULL ";
        } else if ($estado == '2') {
            $query = " AND id_discapacidad_tesoreria IS NULL ";
        }
        return DB::select("SELECT * FROM vw_discapacidad_tesoreria WHERE
        periodo_prestacion BETWEEN ? AND ? AND num_factura LIKE ? $query
         LIMIT ? ", [$desde, $hasta, '%' . $num_factura . '%', $top]);
    }

    public function findTopByListCuilAfi($desde, $hasta, $cuilAfi, $top, $estado)
    {
        $query = "";
        if ($estado == '1') {
            $query = " AND id_discapacidad_tesoreria IS NOT NULL ";
        } else if ($estado == '2') {
            $query = " AND id_discapacidad_tesoreria IS NULL ";
        }
        return DB::select("SELECT * FROM vw_discapacidad_tesoreria WHERE
        periodo_prestacion BETWEEN ? AND ? AND cuil_beneficiario LIKE ? $query
         LIMIT ? ", [$desde, $hasta, '%' . $cuilAfi . '%', $top]);
    }

    public function findTopByListCuilAfiAndCuitPrestador($desde, $hasta, $cuilAfi, $top, $estado, $cuitPrestador)
    {
        $query = "";
        if ($estado == '1') {
            $query = " AND id_discapacidad_tesoreria IS NOT NULL ";
        } else if ($estado == '2') {
            $query = " AND id_discapacidad_tesoreria IS NULL ";
        }
        return DB::select("SELECT * FROM vw_discapacidad_tesoreria WHERE
        periodo_prestacion BETWEEN ? AND ? AND cuil_beneficiario = ? AND cuil_prestador = ? $query
         LIMIT ? ", [$desde, $hasta, $cuilAfi, $cuitPrestador, $top]);
    }

    public function findTopByListCuilAfiAndCuitPrestadorAndNumFactura($desde, $hasta, $cuilAfi, $top, $estado, $cuitPrestador, $numFactura)
    {
        $query = "";
        if ($estado == '1') {
            $query = " AND id_discapacidad_tesoreria IS NOT NULL ";
        } else if ($estado == '2') {
            $query = " AND id_discapacidad_tesoreria IS NULL ";
        }
        return DB::select("SELECT * FROM vw_discapacidad_tesoreria WHERE
            periodo_prestacion BETWEEN ? AND ? AND cuil_beneficiario = ? AND cuil_prestador = ?  AND num_factura = ? $query
            LIMIT ? ", [$desde, $hasta, $cuilAfi, $cuitPrestador, $numFactura, $top]);
    }

    public function findTopByListNumCaeCai($desde, $hasta, $caecai, $top, $estado)
    {
        $query = "";
        if ($estado == '1') {
            $query = " AND id_discapacidad_tesoreria IS NOT NULL ";
        } else if ($estado == '2') {
            $query = " AND id_discapacidad_tesoreria IS NULL ";
        }

        return DB::select("SELECT * FROM vw_discapacidad_tesoreria WHERE
        periodo_prestacion BETWEEN ? AND ? AND num_cae_cai LIKE ? $query
         LIMIT ? ", [$desde, $hasta,   $caecai . '%', $top]);
    }
}
