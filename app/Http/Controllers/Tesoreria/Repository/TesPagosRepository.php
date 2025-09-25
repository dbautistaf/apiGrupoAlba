<?php

namespace App\Http\Controllers\Tesoreria\Repository;

use App\Models\Tesoreria\TesPagoEntity;
use App\Models\Tesoreria\TestDetalleComprobantesPagoEntity;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TesPagosRepository
{
    private $user;
    private $fechaActual;

    public function __construct()
    {
        $this->user = Auth::user();
        $this->fechaActual = Carbon::now('America/Argentina/Buenos_Aires');
    }

    public function findByCrearPago($params)
    {
        return TesPagoEntity::create([
            'id_orden_pago' => $params['id_orden_pago'],
            'id_cuenta_bancaria' => $params['id_cuenta_bancaria'],
            'fecha_registra' => $this->fechaActual,
            'fecha_confirma_pago' => $params['fecha_confirma_pago'],
            'anticipo' => $params['anticipo'],
            'comprobante' => $params['comprobante'],
            'id_forma_pago' => $params['id_forma_pago'],
            'monto_pago' => $params['monto_pago'],
            'observaciones' => $params['observaciones'],
            'id_estado_orden_pago' => $params['id_estado_orden_pago'],
            'id_usuario' => $this->user->cod_usuario,
            'monto_opa' => $params['monto_opa'],
            'recursor' => $params['recursor'],
            'fecha_probable_pago' => $params['fecha_probable_pago'],
            'tipo_factura' => $params['tipo_factura'],
            'pago_emergencia' => $params['pago_emergencia']
        ]);
    }

    public function findByAsignarCodigoVerificacion($id, $codigo)
    {
        $pago = TesPagoEntity::find($id);
        $pago->num_pago = $codigo;
        $pago->update();
        return $pago;
    }

    public function findByListPagosBetween($desde, $hasta)
    {
        return TesPagoEntity::with(['estado', 'cuenta', 'formaPago', 'opa.proveedor', 'opa.factura.razonSocial', 'comprobantes'])
            ->whereBetween(DB::raw('DATE(fecha_probable_pago)'), [$desde, $hasta])
            ->orderBy('id_estado_orden_pago', 'asc')
            ->orderBy('fecha_probable_pago')
            ->get();
    }

    public function findByListPagosFiltroPrincipal($params)
    {
        $jquery = "";
        if ($params->tipo === 'PROVEEDOR') {
            $jquery = TesPagoEntity::with([
                'estado',
                'cuenta',
                'formaPago',
                'opa.proveedor',
                'opa.proveedor.datosBancarios',
                'opa.factura.razonSocial',
                'comprobantes' => function ($query) {
                    $query->where('estado', 1);
                }
            ]);
            $jquery->where('tipo_factura', 'PROVEEDOR');
        } else {
            $jquery = TesPagoEntity::with([
                'estado',
                'cuenta',
                'formaPago',
                'opa.prestador',
                'opa.prestador.datosBancarios',
                'opa.factura.razonSocial',
                'comprobantes' => function ($query) {
                    $query->where('estado', 1);
                }
            ]);
            $jquery->where('tipo_factura', 'PRESTADOR');
        }


        if (!is_null($params->desde) && !is_null($params->hasta)) {
            $jquery->whereBetween(DB::raw('DATE(fecha_probable_pago)'), [$params->desde, $params->hasta]);
        }

        /*  if (!is_null($params->monto_desde) && !is_null($params->monto_hasta)) {
            $jquery->whereBetween('monto_pago', [$params->monto_desde, $params->monto_hasta]);
        } */

        if (!is_null($params->estado)) {
            $jquery->where('id_estado_orden_pago', $params->estado);
        }

        if ($params->pago_urgente == '1') {
            $jquery->where('pago_emergencia', $params->pago_urgente);
        }

        if (!is_null($params->id_locatario)) {
            $jquery->whereHas('opa.factura', function ($query) use ($params) {
                $query->where('id_locatorio', $params->id_locatario);
            });
        }

        if (!is_null($params->id_tipo_imputacion)) {
            $jquery->whereHas('opa.factura', function ($query) use ($params) {
                $query->where('id_tipo_imputacion_sintetizada', $params->id_tipo_imputacion);
            });
        }

        if (!is_null($params->numero)) {
            $jquery->whereHas('opa.factura', function ($query) use ($params) {
                $query->where('numero', $params->numero);
            });
        }

        if (!is_null($params->id_tipo) && $params->id_tipo !== '') {
            $jquery->whereHas('opa.factura', function ($query) use ($params) {
                $query->where('id_tipo_factura', '=', (int) $params->id_tipo);
            });
        }

        // $jquery->orderBy('id_estado_orden_pago', 'asc');
        $jquery->orderBy('fecha_probable_pago');
        return $jquery->get();
    }

    public function findByConfirmarPago($params)
    {
        $pago = TesPagoEntity::find($params->id_pago);
        $pago->monto_opa = $params->monto_opa;
        $pago->id_cuenta_bancaria = $params->id_cuenta_bancaria;
        $pago->fecha_confirma_pago = $params->fecha_confirma_pago;
        $pago->id_forma_pago = $params->id_forma_pago;
        $pago->monto_pago = $params->anticipo == '1' ? $params->monto_anticipado : $params->monto_pago;
        $pago->id_estado_orden_pago = 5;
        $pago->anticipo = $params->anticipo;
        $pago->monto_anticipado = $params->monto_anticipado;
        $pago->num_cheque = $params->num_cheque;
        $pago->fecha_probable_pago = $params->fecha_probable_pago;
        $pago->observaciones = $params->observaciones;

        $pago->id_forma_cobro = is_numeric($params->id_forma_cobro) ? $params->id_forma_cobro : null;
        $pago->monto_cobro = $params->monto_cobro;
        $pago->fecha_confirma_cobro = $params->fecha_confirma_cobro;
        $pago->cuenta_bancaria = $params->cuenta_bancaria;
        $pago->imputacion_contable = $params->imputacion_contable;
        $pago->banco = $params->banco;

        $pago->update();
        return $pago;
    }

    public function findByAnularPago($id_pago, $observacion)
    {
        $pago = TesPagoEntity::find($id_pago);
        $pago->motivo_rechazo = $observacion;
        $pago->id_estado_orden_pago = 3;
        $pago->fecha_rechazo = $this->fechaActual;
        $pago->update();
        return $pago;
    }

    public function findByUpdatePagoPorOpa($params, $idOpa)
    {
        $pago = TesPagoEntity::where('id_orden_pago', $idOpa)->first();
        $pago->fecha_probable_pago = $params->fecha_probable_pago;
        $pago->pago_emergencia = $params->pago_emergencia;
        $pago->update();
        return $pago;
    }

    public function findById($id)
    {
        return TesPagoEntity::find($id);
    }

    public function findBySumarDetallePagosAnticipados($idOpa)
    {
        return (float) TesPagoEntity::where('id_orden_pago', $idOpa)->sum('monto_anticipado');
    }

    public function findByListDetallePagosAnticipadosConfirmados($idOpa)
    {
        return TesPagoEntity::with(['formaPago', 'comprobantes'])
            ->where('id_orden_pago', $idOpa)
            ->where('id_estado_orden_pago', '5')
            ->get();
    }

    public function findByCargarComprobantePago($archivo, $idPago)
    {
        return TestDetalleComprobantesPagoEntity::create([
            'id_pago' => $idPago,
            'nombre_archivo' => $archivo,
            'fecha_registra' => $this->fechaActual,
            'cod_usuario_registra' => $this->user->cod_usuario,
            'estado' => '1'
        ]);
    }

    public function findByDeleteComprobantePago($idPago)
    {
        $archivo = TestDetalleComprobantesPagoEntity::findOrFail($idPago);
        $archivo->cod_usuario_elimina = $this->user->cod_usuario;
        $archivo->fecha_elimina = $this->fechaActual;
        $archivo->estado = '0';
        $archivo->update();
        return $archivo;
    }

    public function findByUpdateOpaPagoFacturaLiquidaciones($idOpa, $monto)
    {
        if (TesPagoEntity::where('id_orden_pago', $idOpa)->where('id_estado_orden_pago', '1')->exists()) {

            if (TesPagoEntity::where('id_orden_pago', $idOpa)->where('id_estado_orden_pago', '1')->where('anticipo', '0')->exists()) {
                DB::update("UPDATE tb_tes_pago SET monto_opa = ?, monto_pago = ? WHERE tipo_factura = 'PRESTADOR' AND id_orden_pago = ? ", [$monto, $monto, $idOpa]);
            } else {
                DB::update("UPDATE tb_tes_pago SET monto_opa = ? WHERE tipo_factura = 'PRESTADOR' AND id_orden_pago = ? ", [$monto, $idOpa]);
            }

            return true;
        }
        return false;
    }
}
