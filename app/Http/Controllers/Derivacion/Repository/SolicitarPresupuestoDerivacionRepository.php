<?php

namespace App\Http\Controllers\Derivacion\Repository;

use App\Http\Controllers\Derivacion\Dto\ParticipantesLicitacionDTO;
use App\Models\Derivaciones\SolicitarPresupuestosDerivacionEntity;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SolicitarPresupuestoDerivacionRepository
{

    private $user;
    private $fechaActual;
    public function __construct()
    {
        $this->user = Auth::user();
        $this->fechaActual = Carbon::now('America/Argentina/Buenos_Aires');
    }

    public function findBySavePrestadores($detalle, $id_derivacion)
    {

        $detalleExistenteDB = SolicitarPresupuestosDerivacionEntity::where('id_derivacion', $id_derivacion)->get();
        $idsGuardar = collect($detalle)->pluck('cod_prestador');
        $idsExistentes = $detalleExistenteDB->pluck('cod_prestador');
        $idsParaEliminar = $idsExistentes->diff($idsGuardar);

        SolicitarPresupuestosDerivacionEntity::where('id_derivacion', $id_derivacion)
            ->whereIn('cod_prestador', $idsParaEliminar)->delete();

        foreach ($detalle as $key) {
            if (!SolicitarPresupuestosDerivacionEntity::where('cod_prestador', $key['cod_prestador'])->where('id_derivacion', $id_derivacion)->exists()) {
                SolicitarPresupuestosDerivacionEntity::create([
                    'id_derivacion' => $id_derivacion,
                    'cod_prestador' => $key['cod_prestador'],
                    'fecha_solicita_presupuesto' => $this->fechaActual,
                    'cod_usuario' => $this->user->cod_usuario
                ]);
            }
        }
    }

    public function findByListParticipantesConvocatoria($idDerivacion)
    {
        return DB::table("vw_participantes_convocatoria_derivaciones")
            ->select('cod_prestador', 'id_solicitud', 'fecha_solicita_presupuesto', 'cuit', 'nombre_fantasia', 'razon_social', 'email', 'celular', 'gano_licitacion', 'archivo_cotizacion', 'monto_cotiza', 'observaciones')
            ->where('id_derivacion', $idDerivacion)
            ->get()
            ->map(function ($row) {
                return new ParticipantesLicitacionDTO(
                    $row->id_solicitud,
                    $row->cod_prestador,
                    $row->cuit,
                    $row->nombre_fantasia,
                    $row->razon_social,
                    $row->fecha_solicita_presupuesto,
                    $row->email,
                    $row->celular,
                    $row->archivo_cotizacion,
                    $row->gano_licitacion,
                    $row->monto_cotiza,
                    $row->observaciones
                );
            });
    }

    public function findByCargarPropuesta($archivo, $idSolicitud)
    {
        $participante = SolicitarPresupuestosDerivacionEntity::find($idSolicitud);
        $participante->archivo_cotizacion = $archivo;
        $participante->update();
    }

    public function findByDetalleId($idSolicitud)
    {
        $participante = SolicitarPresupuestosDerivacionEntity::find($idSolicitud);
        return $participante;
    }

    public function findByAsignarGanadorLicitacion($detalle)
    {
        foreach ($detalle as $key) {
            $participante = SolicitarPresupuestosDerivacionEntity::find($key->id_solicitud);
            $participante->gano_licitacion = $key->ganador;
            $participante->fecha_registra_ganador = $this->fechaActual;
            $participante->cod_usuario_registra_ganador = $this->user->cod_usuario;
            $participante->update();
        }
    }

    public function findBySaveDetalleCotizacion($detalle)
    {
        foreach ($detalle as $value) {
            $item = SolicitarPresupuestosDerivacionEntity::find($value->id_solicitud);
            $item->monto_cotiza = $value->monto_cotiza;
            $item->observaciones = $value->observaciones;
            $item->update();
        }
    }
}
