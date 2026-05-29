<?php

namespace App\Http\Controllers\Internaciones\Repository;

use App\Models\Internaciones\AutorizacionDatosRNEntity;
use App\Models\Internaciones\AutorizacionDetalleRNEntity;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class AutorizacionDatosRNRepository
{
    private $user;
    private $fechaActual;

    public function __construct()
    {
        $this->user = Auth::user();
        $this->fechaActual = Carbon::now('America/Argentina/Buenos_Aires');
    }

    public function findByList($request)
    {
        $query = AutorizacionDatosRNEntity::with(['detalle_prestacion.practica']);
        
        if ($request->has('cod_recien_nacido')) {
            $query->where('cod_recien_nacido', $request->cod_recien_nacido);
        }
        
        if ($request->has('vigente')) {
            $query->where('vigente', $request->vigente);
        }

        return $query->get();
    }

    public function findById($id)
    {
        return AutorizacionDatosRNEntity::with(['detalle_prestacion.practica'])->findOrFail($id);
    }

    public function findBySave($request)
    {
        $parent = AutorizacionDatosRNEntity::create([
            'fecha_registra' => $this->fechaActual,
            'observaciones' => $request->observaciones,
            'fecha_impresion' => $request->fecha_impresion,
            'vigente' => $request->vigente ?? 1,
            'monto_pagar' => $request->monto_pagar ?? 0,
            'usuario_registra' => $this->user->cod_usuario ?? null,
            'usuario_imprime' => $request->usuario_imprime,
            'cod_prestador' => $request->cod_prestador,
            'cod_profesional' => $request->cod_profesional,
            'cod_recien_nacido' => $request->cod_recien_nacido,
            'estado_impresion' => $request->estado_impresion ?? 0,
            'cod_tipo_estado' => $request->cod_tipo_estado ?? 1,
            'diagnostico' => $request->diagnostico,
            'id_diagnostico' => $request->id_diagnostico,
            'domicilio_prestador' => $request->domicilio_prestador,
            'domicilio_profesional' => $request->domicilio_profesional,
            'observacion_interna' => $request->observacion_interna,
        ]);

        if (!empty($request->detalle)) {
            foreach ($request->detalle as $item) {
                $item = (object) $item;
                AutorizacionDetalleRNEntity::create([
                    'cantidad_solicitada' => $item->cantidad_solicitada ?? $item->cantidad ?? 1,
                    'cantidad_autorizada' => $item->cantidad_autorizada ?? $item->cantidad ?? 1,
                    'precio_unitario' => $item->precio_unitario ?? $item->precio_convenio ?? 0,
                    'monto_pagar' => $item->monto_pagar ?? 0,
                    'id_identificador_practica' => $item->id_identificador_practica,
                    'cod_prestacion_rn' => $parent->cod_prestacion_rn,
                    'estado_imprimir' => $item->estado_imprimir ?? 0,
                ]);
            }
        }

        return $parent;
    }

    public function findByUpdate($id, $request)
    {
        $parent = AutorizacionDatosRNEntity::findOrFail($id);
        $parent->update([
            'observaciones' => $request->observaciones ?? $parent->observaciones,
            'fecha_impresion' => $request->fecha_impresion ?? $parent->fecha_impresion,
            'vigente' => $request->vigente ?? $parent->vigente,
            'monto_pagar' => $request->monto_pagar ?? $parent->monto_pagar,
            'usuario_imprime' => $request->usuario_imprime ?? $parent->usuario_imprime,
            'cod_prestador' => $request->cod_prestador ?? $parent->cod_prestador,
            'cod_profesional' => $request->cod_profesional ?? $parent->cod_profesional,
            'cod_recien_nacido' => $request->cod_recien_nacido ?? $parent->cod_recien_nacido,
            'estado_impresion' => $request->estado_impresion ?? $parent->estado_impresion,
            'cod_tipo_estado' => $request->cod_tipo_estado ?? $parent->cod_tipo_estado,
            'diagnostico' => $request->diagnostico ?? $parent->diagnostico,
            'id_diagnostico' => $request->id_diagnostico ?? $parent->id_diagnostico,
            'domicilio_prestador' => $request->domicilio_prestador ?? $parent->domicilio_prestador,
            'domicilio_profesional' => $request->domicilio_profesional ?? $parent->domicilio_profesional,
            'observacion_interna' => $request->observacion_interna ?? $parent->observacion_interna,
            'fecha_modifica' => $this->fechaActual,
        ]);

        if (isset($request->detalle)) {
            // Delete old details
            AutorizacionDetalleRNEntity::where('cod_prestacion_rn', $parent->cod_prestacion_rn)->delete();

            // Save new details
            foreach ($request->detalle as $item) {
                $item = (object) $item;
                AutorizacionDetalleRNEntity::create([
                    'cantidad_solicitada' => $item->cantidad_solicitada ?? $item->cantidad ?? 1,
                    'cantidad_autorizada' => $item->cantidad_autorizada ?? $item->cantidad ?? 1,
                    'precio_unitario' => $item->precio_unitario ?? $item->precio_convenio ?? 0,
                    'monto_pagar' => $item->monto_pagar ?? 0,
                    'id_identificador_practica' => $item->id_identificador_practica,
                    'cod_prestacion_rn' => $parent->cod_prestacion_rn,
                    'estado_imprimir' => $item->estado_imprimir ?? 0,
                ]);
            }
        }

        return $parent;
    }

    public function findByDeleteId($id)
    {
        $parent = AutorizacionDatosRNEntity::findOrFail($id);
        AutorizacionDetalleRNEntity::where('cod_prestacion_rn', $parent->cod_prestacion_rn)->delete();
        return $parent->delete();
    }
}
