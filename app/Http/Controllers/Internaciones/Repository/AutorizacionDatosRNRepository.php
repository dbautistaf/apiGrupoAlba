<?php

namespace App\Http\Controllers\Internaciones\Repository;

use App\Models\afiliado\AfiliadoPadronEntity;
use App\Models\Internaciones\AutorizacionDatosRNEntity;
use App\Models\Internaciones\AutorizacionDetalleRNEntity;
use App\Models\Internaciones\RecienNacidoEntity;
use App\Models\PrestacionesMedicas\DetallePrestacionesPracticaLaboratorioEntity;
use App\Models\PrestacionesMedicas\DetalleTramitePrestacionMedicaEntity;
use App\Models\PrestacionesMedicas\PrestacionesPracticaLaboratorioEntity;
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
            'id_locatorio' => $request->id_locatorio,
            'cod_sindicato' => $request->cod_sindicato,
            'id_tipo_tramite' => $request->id_tipo_tramite,
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
            'id_locatorio' => $request->id_locatorio,
            'cod_sindicato' => $request->cod_sindicato,
            'id_tipo_tramite' => $request->id_tipo_tramite,
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

    public function migrarAutorizaciones($cod_recien_nacido, $dni_rn)
    {
        // 1. Obtener y actualizar al recién nacido
        $recienNacido = RecienNacidoEntity::findOrFail($cod_recien_nacido);
        $recienNacido->dni_rn = $dni_rn;
        $recienNacido->save();

        // 2. Obtener los datos del afiliado
        $afiliado = AfiliadoPadronEntity::where('dni', $dni_rn)->first();

        // 3. Buscar todas las autorizaciones temporales del RN
        $autorizacionesRn = AutorizacionDatosRNEntity::where('cod_recien_nacido', $cod_recien_nacido)->get();
        $migratedCount = 0;

        foreach ($autorizacionesRn as $authRn) {
            // A. Crear el detalle del trámite general
            $detalleTramite = DetalleTramitePrestacionMedicaEntity::create([
                'id_locatorio' => $authRn->id_locatorio,
                'cod_sindicato' => $authRn->cod_sindicato,
                'id_tipo_tramite' => $authRn->id_tipo_tramite,
                'id_tipo_prioridad' => 1
            ]);

            // B. Crear la cabecera de la prestación médica general
            $prestacion = PrestacionesPracticaLaboratorioEntity::create([
                'fecha_registra' => $authRn->fecha_registra,
                'observaciones' => 'AUTORIZACIÓN RN MIGRADA: ' . ($authRn->observaciones ?? ''),
                'fecha_impresion' => $authRn->fecha_impresion,
                'vigente' => $authRn->vigente ?? 1,
                'monto_pagar' => $authRn->monto_pagar ?? 0,
                'archivo_adjunto' => null,
                'usuario_registra' => $authRn->usuario_registra ?? ($this->user->cod_usuario ?? null),
                'usuario_imprime' => $authRn->usuario_imprime,
                'cod_prestador' => $authRn->cod_prestador,
                'cod_profesional' => $authRn->cod_profesional,
                'dni_afiliado' => $dni_rn,
                'estado_impresion' => $authRn->estado_impresion ?? 0,
                'cod_tipo_estado' => $authRn->cod_tipo_estado ?? 2,
                'diagnostico' => $authRn->diagnostico,
                'id_diagnostico' => $authRn->id_diagnostico,
                'domicilio_prestador' => $authRn->domicilio_prestador,
                'domicilio_profesional' => $authRn->domicilio_profesional,
                'edad_afiliado' => 0,
                'cod_internacion' => $recienNacido->cod_internacion,
                'id_detalle_tramite' => $detalleTramite->id_detalle_tramite,
                'observacion_interna' => $authRn->observacion_interna
            ]);

            // C. Copiar los detalles de la práctica
            $detallesRn = AutorizacionDetalleRNEntity::where('cod_prestacion_rn', $authRn->cod_prestacion_rn)->get();
            foreach ($detallesRn as $detRn) {
                DetallePrestacionesPracticaLaboratorioEntity::create([
                    'cantidad_solicitada' => $detRn->cantidad_solicitada ?? 1,
                    'cantidad_autorizada' => $detRn->cantidad_autorizada ?? 1,
                    'precio_unitario' => $detRn->precio_unitario ?? 0,
                    'monto_pagar' => $detRn->monto_pagar ?? 0,
                    'id_identificador_practica' => $detRn->id_identificador_practica,
                    'cod_prestacion' => $prestacion->cod_prestacion,
                    'estado_imprimir' => $detRn->estado_imprimir ?? '0'
                ]);
            }

            // D. Eliminar los registros de recién nacido originales
            AutorizacionDetalleRNEntity::where('cod_prestacion_rn', $authRn->cod_prestacion_rn)->delete();
            $authRn->delete();

            $migratedCount++;
        }

        return $migratedCount;
    }
}
