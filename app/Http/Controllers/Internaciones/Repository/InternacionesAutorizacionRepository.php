<?php

namespace App\Http\Controllers\Internaciones\Repository;

use App\Models\Internaciones\AutorizacionRecienNacidoEntity;
use App\Models\Internaciones\InternacionAutorizacionEntity;
use App\Models\Internaciones\RecienNacidoEntity;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class InternacionesAutorizacionRepository
{
    //
    private $user;
    private $fechaActual;
    public function __construct()
    {
        $this->user = Auth::user();
        $this->fechaActual = Carbon::now('America/Argentina/Buenos_Aires');
    }

    public function findBySave($detalle, $cod_internacion)
    {

        if (!empty($detalle) && !empty($cod_internacion)) {
            foreach ($detalle as $key) {
                InternacionAutorizacionEntity::create([
                    'cod_internacion' => $cod_internacion,
                    'cod_prestacion' => $key,
                    'fecha_registra' => $this->fechaActual,
                    'cod_usuario' => $this->user->cod_usuario,
                ]);
            }
        }
    }

    public function findByUpdate($detalle, $cod_internacion)
    {
        if (!empty($detalle) && !empty($cod_internacion)) {
            InternacionAutorizacionEntity::where('cod_internacion', $cod_internacion)->delete();

            foreach ($detalle as $key) {
                InternacionAutorizacionEntity::create([
                    'cod_internacion' => $cod_internacion,
                    'cod_prestacion' => $key,
                    'fecha_registra' => $this->fechaActual,
                    'cod_usuario' => $this->user->cod_usuario,
                ]);
            }
        }
    }

    public function findBySavePrestacionVinculada($request)
    {
        InternacionAutorizacionEntity::create([
            'cod_internacion' => $request->cod_internacion,
            'cod_prestacion' => $request->id_internacion_autorizacion,
            'fecha_registra' => $this->fechaActual,
            'cod_usuario' => $this->user->cod_usuario,
        ]);
    }

    public function findByDeletePrestacionVinculada($cod_prestacion)
    {
        $internacion = InternacionAutorizacionEntity::where('cod_prestacion', $cod_prestacion)->first();
        if ($internacion != null) {
            $internacion->delete();
        }
    }

    public function findBySaveRN($request)
    {
        if (!empty($request->cod_recien_nacido)) {
            $query = RecienNacidoEntity::find($request->cod_recien_nacido);
            $query->dni_rn = $request->dni_rn;
            $query->nombre_rn = $request->nombre_rn;
            $query->apellidos_rn = $request->apellidos_rn;
            $query->fecha_nacimiento = $request->fecha_nacimiento;
            $query->diagnostico = $request->diagnostico;
            $query->observaciones = $request->observaciones;
            $query->save();
        } else {
            RecienNacidoEntity::create([
                'dni_rn' => $request->dni_rn,
                'nombre_rn' => $request->nombre_rn,
                'apellidos_rn' => $request->apellidos_rn,
                'fecha_nacimiento' => $request->fecha_nacimiento,
                'diagnostico' => $request->diagnostico,
                'observaciones' => $request->observaciones,
                'cod_internacion' => $request->cod_internacion,
                'fecha_registra' => $this->fechaActual,
                'cod_usuario' => $this->user->cod_usuario,
            ]);
        }
    }

    public function findByDeleteRN($request)
    {
        $internacion = RecienNacidoEntity::find($request->cod_recien_nacido);
        if ($internacion != null) {
            $internacion->delete();
        }
    }

    public function findBySavePrestacionVinculadaRN($request)
    {
        $internacion = AutorizacionRecienNacidoEntity::where('cod_prestacion', $request->cod_prestacion)
            ->where('cod_recien_nacido', $request->cod_recien_nacido)->first();
        if (!$internacion) {
            AutorizacionRecienNacidoEntity::create([
                'cod_recien_nacido' => $request->cod_recien_nacido,
                'cod_prestacion' => $request->cod_prestacion,
                'fecha_registra' => $this->fechaActual,
                'cod_usuario' => $this->user->cod_usuario,
            ]);
        }
    }

    public function findByListAutorizacionRN($request)
    {
        return AutorizacionRecienNacidoEntity::with(['detalle_prestacion.practica', 'internacion'])
            ->where('cod_recien_nacido', $request->cod_recien_nacido)->get();
    }

    public function findByDeletePrestacionVinculadaRN($cod_prestacion)
    {
        $internacion = AutorizacionRecienNacidoEntity::where('cod_prestacion', $cod_prestacion)->first();
        if ($internacion != null) {
            $internacion->delete();
        }

        // También eliminar si es una autorización directa de recién nacido
        $direct = \App\Models\Internaciones\AutorizacionDatosRNEntity::find($cod_prestacion);
        if ($direct != null) {
            \App\Models\Internaciones\AutorizacionDetalleRNEntity::where('cod_prestacion_rn', $direct->cod_prestacion_rn)->delete();
            $direct->delete();
        }
    }
}
