<?php

namespace   App\Http\Controllers\PrestacionesMedicas\Repository;

use App\Models\Internaciones\InternacionesEntity;
use App\Models\PrestacionesMedicas\AuditarPrestacionesPracticaLaboratorioEntity;
use App\Models\PrestacionesMedicas\DetallePrestacionesPracticaLaboratorioEntity;
use App\Models\PrestacionesMedicas\PrestacionesPracticaLaboratorioEntity;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class AuditarPrestacionesMedicasRepository
{
    private $user;
    private $fechaActual;
    public function __construct()
    {
        $this->user = Auth::user();
        $this->fechaActual = Carbon::now('America/Argentina/Buenos_Aires');
    }


    public function findByAutorizarItemDetallePrestacion($params)
    {
        $nuevoMonto  = 0;
        $detalle = DetallePrestacionesPracticaLaboratorioEntity::find($params->cod_detalle);
        $nuevoMonto = $detalle->precio_unitario * $params->cantidad_autorizada;
        $detalle->cantidad_autorizada = $params->cantidad_autorizada;
        $detalle->monto_pagar = $nuevoMonto;
        $detalle->estado_autoriza = ($params->estado_autoriza ? 'SI' : 'NO');
        $detalle->estado_imprimir = '1';
        $detalle->update();

        return $nuevoMonto;
    }

    public function findByDenegarAutorizacionItemDetallePrestacion($params)
    {
        $detalle = DetallePrestacionesPracticaLaboratorioEntity::find($params->cod_detalle);
        $detalle->cantidad_autorizada = $params->cantidad_autorizada;
        $detalle->estado_autoriza = ($params->estado_autoriza ? 'SI' : 'NO');
        $detalle->estado_imprimir = '1';
        $detalle->update();

        return $detalle;
    }

    public function finByRegistrarAuditoria($key, $observacion)
    {
        $query = AuditarPrestacionesPracticaLaboratorioEntity::where('cod_detalle', $key->cod_detalle)->first();
        if ($query) {
            $query->cod_tipo_rechazo = ($key->cot_tipo_rechazo == '' ? null : $key->cot_tipo_rechazo);
            $query->observaciones = $key->observacion_rechazo;
            $query->estado_autoriza = $key->estado_autoriza;
            $query->fecha_autorizacion = $this->fechaActual;
            $query->cod_usuario_audita = $this->user->cod_usuario;
            $query->save();
            return $query;
        } else {
            return  AuditarPrestacionesPracticaLaboratorioEntity::create([
                'fecha_autorizacion' => $this->fechaActual,
                'cod_usuario_audita' => $this->user->cod_usuario,
                'observaciones' =>  $key->observacion_rechazo,
                'cod_tipo_rechazo' => ($key->cot_tipo_rechazo == '' ? null : $key->cot_tipo_rechazo),
                'cod_detalle' => $key->cod_detalle,
                'estado_autoriza' => ($key->estado_autoriza ? 'SI' : 'NO'),
                'observacion_auditoria_medica' => $observacion
            ]);
        }
    }

    public function findByUpdatePrestacionMedica($cod_prestacion, $autorizados, $totalItems, $noAutorizados, $sumaTotalAuditado)
    {
        $prestacionLab = PrestacionesPracticaLaboratorioEntity::find($cod_prestacion);
        if ($autorizados == $totalItems) {
            $prestacionLab->cod_tipo_estado = 1;
        } else if ($noAutorizados == $totalItems) {
            $prestacionLab->cod_tipo_estado = 3;
        } else {
            $prestacionLab->cod_tipo_estado = 6;
        }

        $prestacionLab->monto_pagar = $sumaTotalAuditado;
        $prestacionLab->update();
        if (is_numeric($prestacionLab->cod_internacion)) {
            $internacion = InternacionesEntity::find($prestacionLab->cod_internacion);
            $internacion->cod_tipo_estado_detalle_prestacion =  $prestacionLab->cod_tipo_estado;
            $internacion->update();
        }
    }

    public function findByListAfiliado($params)
    {
        return DetallePrestacionesPracticaLaboratorioEntity::with(['prestacion', 'practica'])
            ->whereHas('prestacion', function ($query) use ($params) {
                $query->where('dni_afiliado', $params->dni);
                $query->whereBetween('fecha_registra', [$params->desde, $params->hasta]);
            })
            ->whereNotNull('estado_autoriza')
            ->orderByDesc('cod_detalle')
            ->get();
    }
}
