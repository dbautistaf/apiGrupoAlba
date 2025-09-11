<?php

namespace App\Http\Controllers\Internaciones\Repository;

use App\Models\Internaciones\IntDomPresupuestoEntity;
use App\Models\Internaciones\InternacionDomiciliariaDetalleEntity;
use App\Models\Internaciones\IntDomSolicitarPresupuestoEntity;
use App\Models\Internaciones\InternacionDomiciliariaHistorialCostoEntity;
use App\Models\Internaciones\InternacionDomiciliariaServiciosEntity;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Log;

class InternacionDomiciliariaPresupuestosRepository
{
    private $user;
    private $fechaActual;

    public function __construct()
    {
        $this->user = Auth::user();
        $this->fechaActual = Carbon::now('America/Argentina/Buenos_Aires');
    }

    public function findByAddItemSolicitud($item)
    {
        IntDomSolicitarPresupuestoEntity::create([
            'id_internacion_domiciliaria' => $item->id_internacion_domiciliaria,
            'cod_prestador' => $item->cod_prestador,
            'fecha_solicita_presupuesto' => $this->fechaActual,
            'cod_usuario' => $this->user->cod_usuario
        ]);
    }

    public function findByUpdateItemSolicitud($item)
    {
        $presupuesto = IntDomSolicitarPresupuestoEntity::find($item->id_solicitud);
        $presupuesto->cod_prestador = $item->cod_prestador;
        $presupuesto->update();
    }

    public function findByDeleteItemSolicitud($id)
    {
        $presupuesto = IntDomSolicitarPresupuestoEntity::find($id);
        return $presupuesto->delete();
    }

    public function findBySolicitudId($id)
    {
        return IntDomSolicitarPresupuestoEntity::find($id);
    }

    public function findBySolicitarPresupuestos($detalle, $id)
    {
        foreach ($detalle as $key) {
            $entity = (object) $key;
            if (
                IntDomSolicitarPresupuestoEntity::where('id_internacion_domiciliaria', $id)
                    ->where('cod_prestador', $entity->cod_prestador)
                    ->exists()
            ) {
                $this->findByUpdateItemSolicitud($entity);
            } else {
                $this->findByAddItemSolicitud($entity);
            }
        }
    }

    public function findByListParticipantes($id)
    {
        return IntDomSolicitarPresupuestoEntity::with(['prestador'])
            ->where('id_internacion_domiciliaria', $id)
            ->get();
    }

    public function findByListDetalleServicios($id)
    {
        return InternacionDomiciliariaDetalleEntity::with('servicio')
            ->where('id_internacion_domiciliaria', $id)->get();
    }

    public function findByAgregarAdjuntoSolicitud($id, $adjunto)
    {
        $presupuesto = IntDomSolicitarPresupuestoEntity::find($id);
        $presupuesto->archivo_cotizacion = $adjunto;
        $presupuesto->update();
    }

    public function findByDeleteAdjuntoSolicitud($id)
    {
        $presupuesto = IntDomSolicitarPresupuestoEntity::find($id);
        $presupuesto->adjunto = null;
        $presupuesto->update();
    }

    public function findByCargarItemPresupuesto($item)
    {
        return IntDomPresupuestoEntity::create([
            'id_detalle' => $item->id_detalle,
            'cantidad_autorizada' => $item->cantidad_autoriza,
            'monto_cotiza' => $item->monto_cotiza,
            'observaciones' => $item->observaciones,
            'id_solicitud' => $item->id_solicitud,
            'fecha_registra' => $this->fechaActual,
            'cod_usuario' => $this->user->cod_usuario,
            'importe_total' =>$item->importe_total,
        ]);
    }

    public function findByPresupuestoDetalle($idDetalle, $idSolicitud)
    {
        return IntDomPresupuestoEntity::where('id_detalle', $idDetalle)
            ->where('id_solicitud', $idSolicitud)
            ->first();
    }

    public function findByUpdateItemPresupuesto($item)
    {
        $presupuesto = IntDomPresupuestoEntity::find($item->id_cotizacion);
        $presupuesto->cantidad_autorizada = $item->cantidad_autoriza;
        $presupuesto->monto_cotiza = $item->monto_cotiza;
        $presupuesto->observaciones = $item->observaciones;
        $presupuesto->update();
    }

    public function findByCargarPresupuestoSolicitado($detalle)
    {
        foreach ($detalle as $key) {
            foreach ($key->item as $val) {
                if (is_null($val->id_cotizacion)) {
                    $this->findByCargarItemPresupuesto($val);
                } else {
                    $this->findByUpdateItemPresupuesto($val);
                }
            }
        }
    }

    public function findByAsignarGanadorPresupuesto($detalle)
    {
        foreach ($detalle as $key) {
            $presupuesto = IntDomSolicitarPresupuestoEntity::find($key->id_solicitud);
            $presupuesto->gano_licitacion = $key->ganador;
            $presupuesto->fecha_registra_ganador = $this->fechaActual;
            $presupuesto->cod_usuario_registra_ganador = $this->user->cod_usuario;
            $presupuesto->update();
        }
    }

    public function findByHistorialCosto($detalle,$id)
    {
        foreach ($detalle as $key) {
            foreach ($key->item as $val) {
                if (!is_null($val->id_cotizacion)) {
                    $presupuesto = IntDomPresupuestoEntity::find($val->id_cotizacion);
                    if($val->monto_cotiza > $presupuesto->monto_cotiza){
                        InternacionDomiciliariaHistorialCostoEntity::create([
                            'id_servicio' => $val->id_servicio,
                            'fecha_ajuste' => $this->fechaActual,
                            'monto_anterior' => $presupuesto->monto_cotiza,
                            'monto_nuevo' => $val->monto_cotiza,
                            'id_internacion_domiciliaria' => $id,
                            'cod_usuario' => $this->user->cod_usuario
                        ]);
                    }
                    $presupuesto->cantidad_autorizada = $val->cantidad_autoriza;
                    $presupuesto->monto_cotiza = $val->monto_cotiza;
                    $presupuesto->observaciones = $val->observaciones;
                    $presupuesto->update();
                }
            }
        }
    }
}
