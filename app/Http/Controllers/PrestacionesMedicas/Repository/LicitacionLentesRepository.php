<?php

namespace App\Http\Controllers\PrestacionesMedicas\Repository;

use App\Models\PrestacionesMedicas\LentesPrestadoresLicitacionEntity;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class LicitacionLentesRepository
{
    private $user;
    private $fechaActual;
    public function __construct()
    {
        $this->user = Auth::user();
        $this->fechaActual = Carbon::now('America/Argentina/Buenos_Aires');
    }

    public function findBySavePrestadores($detalle, $id)
    {
        $detalleExistenteDB = LentesPrestadoresLicitacionEntity::where('id_solitud_lente', $id)->get();
        $idsGuardar = collect($detalle)->pluck('cod_prestador');
        $idsExistentes = $detalleExistenteDB->pluck('cod_prestador');
        $idsParaEliminar = $idsExistentes->diff($idsGuardar);

        LentesPrestadoresLicitacionEntity::where('id_solitud_lente', $id)
            ->whereIn('cod_prestador', $idsParaEliminar)->delete();

        foreach ($detalle as $key) {
            if (!LentesPrestadoresLicitacionEntity::where('cod_prestador', $key['cod_prestador'])->where('id_solitud_lente', $id)->exists()) {
                LentesPrestadoresLicitacionEntity::create([
                    'id_solitud_lente' => $id,
                    'cod_prestador' => $key['cod_prestador'],
                    'fecha_solicita_presupuesto'  => $this->fechaActual,
                    'cod_usuario'  => $this->user->cod_usuario
                ]);
            }
        }
    }

    public function findByListParticipantes($id)
    {
        return LentesPrestadoresLicitacionEntity::with(['prestador'])
            ->where('id_solitud_lente', $id)->get();
    }

    public function findByCargarArchivoPropuesta($archivo, $id)
    {
        $participante = LentesPrestadoresLicitacionEntity::find($id);
        $participante->archivo_cotizacion = $archivo;
        $participante->update();
        return $participante;
    }

    public function findByPropuestaId($id)
    {
        return LentesPrestadoresLicitacionEntity::find($id);
    }

    public function findBySaveDetallePresupuesto($detalle)
    {
        foreach ($detalle as $key) {
            $presupuesto = LentesPrestadoresLicitacionEntity::find($key->id_solicitud);
            $presupuesto->monto_cotiza = $key->monto_cotiza;
            $presupuesto->observaciones = $key->observaciones;
            $presupuesto->update();
        }
    }

    public function findByAsignarGanador($detalle)
    {
        $isExistedGanador = false;
        foreach ($detalle as $key) {
            if ($key->ganador == '1') {
                $presupuesto = LentesPrestadoresLicitacionEntity::find($key->id_solicitud);
                $presupuesto->gano_licitacion = '1';
                $presupuesto->fecha_registra_ganador = $this->fechaActual;
                $presupuesto->cod_usuario_registra_ganador = $this->user->cod_usuario;
                $presupuesto->update();
                $isExistedGanador = true;
            }
        }

        return $isExistedGanador;
    }
}
