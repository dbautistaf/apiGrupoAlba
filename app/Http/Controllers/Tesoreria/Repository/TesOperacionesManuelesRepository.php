<?php

namespace App\Http\Controllers\Tesoreria\Repository;

use App\Models\Tesoreria\TesOperacionesManualesEntity;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TesOperacionesManuelesRepository
{

    private $user;
    private $fechaActual;

    public function __construct()
    {
        $this->user = Auth::user();
        $this->fechaActual = Carbon::now('America/Argentina/Buenos_Aires');
    }

    public function findByNuevaOperacion($parametros, $documento)
    {
        return TesOperacionesManualesEntity::create([
            'id_tipo_transaccion' => $parametros->id_tipo_transaccion,
            'fecha_operacion' => $parametros->fecha_operacion,
            'id_cuenta_bancaria' => $parametros->id_cuenta_bancaria,
            'monto_operacion' => $parametros->monto_operacion,
            'id_tipo_moneda' => $parametros->id_tipo_moneda,
            'observaciones' => $parametros->observaciones,
            'comprobante' => $documento,
            'estado_operacion' => $parametros->estado_operacion,
            'id_usuario' => $this->user->cod_usuario,
            'fecha_registra' => $this->fechaActual,
            'id_cuenta_bancaria_destino' => !is_numeric($parametros->id_cuenta_bancaria_destino) ? null : $parametros->id_cuenta_bancaria_destino,
            'monto_retencion' => $parametros->monto_retencion,
            'num_factura' => $parametros->num_factura
        ]);
    }

    public function findByAnularOperacion($id, $obs)
    {
        $ope = TesOperacionesManualesEntity::find($id);
        $ope->motivo_anulacion = $obs;
        $ope->id_usuario_modifica = $this->user->cod_usuario;
        $ope->fecha_modifica = $this->fechaActual;
        $ope->estado_operacion = 'ANULADA';
        $ope->save();
        return $ope;
    }

    public function findByEnlazarOperacionFacturaObraSocial($params, $archivo)
    {
        $ope = TesOperacionesManualesEntity::find($params->id_operacion);
        $ope->comprobante = $archivo;
        $ope->num_factura = $params->num_factura;
        $ope->id_usuario_modifica = $this->user->cod_usuario;
        $ope->fecha_modifica = $this->fechaActual;
        $ope->save();
        return $ope;
    }


    public function findByListarBetween($desde, $hasta)
    {
        return TesOperacionesManualesEntity::with(['cuenta', 'tipoMoneda', 'transaccion', 'usuario', 'destino'])
            ->whereBetween(DB::raw('DATE(fecha_registra)'), [$desde, $hasta])
            ->orderByDesc('id_operacion')
            ->get();
    }

    public function findByListarBetweenCuenta($desde, $hasta, $cuenta)
    {
        return TesOperacionesManualesEntity::with(['cuenta', 'tipoMoneda', 'transaccion', 'usuario', 'destino'])
            ->where('id_cuenta_bancaria', $cuenta)
            ->whereBetween(DB::raw('DATE(fecha_registra)'), [$desde, $hasta])
            ->orderByDesc('id_operacion')
            ->get();
    }

    public function findById($id)
    {
        return TesOperacionesManualesEntity::find($id);
    }
}
