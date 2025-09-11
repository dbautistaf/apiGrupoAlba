<?php

namespace App\Http\Controllers\medicacion_alto_costo\Repository;

use App\Models\medicacionAltoCosto\DetalleComprobantesEntity;
use App\Models\medicacionAltoCosto\MedicacionAltoCosto;
use App\Models\medicacionAltoCosto\MedicacionAltoCostoDetalle;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class MedicacionAltoCostoRepository
{
    private $user;
    private $fechaActual;
    public function __construct()
    {
        $this->user = Auth::user();
        $this->fechaActual = Carbon::now('America/Argentina/Buenos_Aires');
    }

    public function findByNumeroTramite()
    {
        $latestNumeroTramite = MedicacionAltoCosto::latest('numero_tramite')->first();
        $numeroTramite = $latestNumeroTramite ? $latestNumeroTramite->numero_tramite + 1 : 1;
        return $numeroTramite;
    }

    public function findByIdComprobante($id)
    {
        return DetalleComprobantesEntity::find($id);
    }

    public function findByCrear($params)
    {
        return MedicacionAltoCosto::create([
            'dni_afiliado' => $params->dni_afiliado,
            'id_tipo_autorizacion' => $params->id_tipo_autorizacion,
            'matricula_medico' => $params->matricula_medico,
            'nombre_medico' => $params->nombre_medico,
            'numero_tramite' => $this->findByNumeroTramite(),
            'id_estado_tratamiento' => $params->id_estado_tratamiento,
            'fecha_autorizacion' => $params->fecha_autorizacion,
            'id_estado_pago' => $params->id_estado_pago,
            'importe_cubierto' => $params->importe_cubierto ?? '0.00',
            'importe_afiliado' => $params->importe_afiliado ?? '0.00',
            'fecha_entrega' => !empty($params->fecha_entrega) ? $params->fecha_entrega : null,
            'id_modo_entrega' => !empty($params->id_modo_entrega) ? $params->id_modo_entrega : null,
            'responsable_entrega' => $params->responsable_entrega,
            'diagnostico' => $params->diagnostico,
            'indicaciones_medicas' => $params->indicaciones_medicas,
            'observaciones' => $params->observaciones,
            'archivo' => null,
            'fecha_registro' => $this->fechaActual,
            'id_usuario' => $this->user->cod_usuario,
        ]);
    }

    public function findByUpdate($params)
    {
        $med = MedicacionAltoCosto::find($params->id_medicacion_alto_costo);
        $med->dni_afiliado = $params->dni_afiliado;
        $med->id_tipo_autorizacion = $params->id_tipo_autorizacion;
        $med->matricula_medico = $params->matricula_medico;
        $med->nombre_medico = $params->nombre_medico;
        $med->id_estado_tratamiento = $params->id_estado_tratamiento;
        $med->fecha_autorizacion = $params->fecha_autorizacion;
        $med->id_estado_pago = $params->id_estado_pago;
        $med->importe_cubierto = $params->importe_cubierto ?? '0.00';
        $med->importe_afiliado = $params->importe_afiliado ?? '0.00';
        $med->fecha_entrega = $params->fecha_entrega;
        $med->id_modo_entrega = $params->id_modo_entrega;
        $med->responsable_entrega = $params->responsable_entrega;
        $med->diagnostico = $params->diagnostico;
        $med->indicaciones_medicas = $params->indicaciones_medicas;
        $med->observaciones = $params->observaciones;
        $med->update();
        return $med;
    }

    public function findByAgregarDetalleArchivos($archivosAdjuntos, $medicacion)
    {
        foreach ($archivosAdjuntos as $key) {
            DetalleComprobantesEntity::create([
                'nombre_archivo' => $key['nombre'],
                'fecha_registra' => $this->fechaActual,
                'activo' => '1',
                'id_medicacion_alto_costo' => $medicacion->id_medicacion_alto_costo
            ]);
        }
    }

    public function findByCrearDetalleMedicamentos($detalle, $medicacion)
    {
        foreach ($detalle as $item) {
            if (is_numeric($item->id_detalle)) {
                $this->findByUpdateItemDetalleMedicamentos($item, $medicacion->id_medicacion_alto_costo);
            } else {
                $this->findByCrearItemDetalleMedicamentos($item, $medicacion->id_medicacion_alto_costo);
            }
        }
    }

    public function findByCrearItemDetalleMedicamentos($item, $idmedicacion)
    {
        return MedicacionAltoCostoDetalle::create([
            'id_medicacion_alto_costo' => $idmedicacion,
            'id_vademecum' => $item->id_vademecum,
            'cantidad' => $item->cantidad,
            'id_cobertura' => $item->id_cobertura,
            'precio_unitario' => $item->precio_unitario,
            'precio_total' => $item->precio_total,
            'fecha_registro' => $this->fechaActual
        ]);
    }

    public function findByUpdateItemDetalleMedicamentos($item, $idmedicacion)
    {
        $value = MedicacionAltoCostoDetalle::find($item->id_detalle);
        $value->id_medicacion_alto_costo = $idmedicacion;
        $value->id_vademecum = $item->id_vademecum;
        $value->cantidad = $item->cantidad;
        $value->id_cobertura = $item->id_cobertura;
        $value->precio_unitario = $item->precio_unitario;
        $value->precio_total = $item->precio_total;
        $value->update();
    }

    public function findByEliminarItenDetalle($item)
    {
        $value = MedicacionAltoCostoDetalle::find($item->id_detalle);
        $value->estado_registro = $item->estado;
        $value->update();
    }

    public function findByEliminarMedicacionAltoCosto($item)
    {
        $value = MedicacionAltoCosto::find($item->id_medicacion_alto_costo);
        $value->estado_registro = $item->estado;
        $value->update();
    }
}
