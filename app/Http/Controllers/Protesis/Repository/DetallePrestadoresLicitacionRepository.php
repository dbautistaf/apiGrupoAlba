<?php

namespace App\Http\Controllers\Protesis\Repository;

use App\Http\Controllers\Protesis\Dto\ParticipantesConvocatoriaDto;
use App\Models\Protesis\DetallePrestadoresLicitacionEntity;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DetallePrestadoresLicitacionRepository
{

    private $user;
    private $fechaActual;
    public function __construct()
    {
        $this->user = Auth::user();
        $this->fechaActual = Carbon::now('America/Argentina/Buenos_Aires');
    }

    public function findBySavePrestadores($detalle, $idProtesis)
    {

        $detalleExistenteDB = DetallePrestadoresLicitacionEntity::where('id_protesis', $idProtesis)->get();
        $idsGuardar = collect($detalle)->pluck('cod_prestador');
        $idsExistentes = $detalleExistenteDB->pluck('cod_prestador');
        $idsParaEliminar = $idsExistentes->diff($idsGuardar);

        DetallePrestadoresLicitacionEntity::where('id_protesis', $idProtesis)
            ->whereIn('cod_prestador', $idsParaEliminar)->delete();

        foreach ($detalle as $key) {
            if (!DetallePrestadoresLicitacionEntity::where('cod_prestador', $key['cod_prestador'])->where('id_protesis', $idProtesis)->exists()) {
                DetallePrestadoresLicitacionEntity::create([
                    'id_protesis' => $idProtesis,
                    'cod_prestador' => $key['cod_prestador'],
                    'fecha_solicita_presupuesto'  => $this->fechaActual,
                    'cod_usuario'  => $this->user->cod_usuario
                ]);
            }
        }
    }

    public function findByListParticipantesConvocatoria($idProtesis)
    {
        return DB::table("vw_participantes_convocatoria_protesis")
            ->select('cod_prestador', 'id_solicitud', 'fecha_solicita_presupuesto', 'cuit', 'nombre_fantasia', 'razon_social', 'email', 'celular', 'gano_licitacion', 'archivo_cotizacion')
            ->where('id_protesis', $idProtesis)
            ->get()
            ->map(function ($row) {
                return new ParticipantesConvocatoriaDto(
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
                    null
                );
            });
    }

    public function findByCargarPropuesta($archivo, $idSolicita)
    {
        $participante = DetallePrestadoresLicitacionEntity::find($idSolicita);
        $participante->archivo_cotizacion = $archivo;
        $participante->update();
    }

    public function findByDetalleId($idSolicita)
    {
        $participante = DetallePrestadoresLicitacionEntity::find($idSolicita);
        return $participante;
    }

    public function findByAsignarGanadorLicitacion($detalle)
    {
        foreach ($detalle as $key) {
            $participante = DetallePrestadoresLicitacionEntity::find($key->id_solicitud);
            $participante->gano_licitacion = $key->ganador;
            $participante->fecha_registra_ganador = $this->fechaActual;
            $participante->cod_usuario_registra_ganador = $this->user->cod_usuario;
            $participante->update();
        }
    }
}
