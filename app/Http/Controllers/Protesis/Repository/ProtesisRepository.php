<?php

namespace   App\Http\Controllers\Protesis\Repository;

use App\Models\Protesis\DetalleComprobanteProtesis;
use App\Models\Protesis\ProtesisDetalleEntity;
use App\Models\Protesis\ProtesisEntity;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ProtesisRepository
{

    private $user;
    private $fechaActual;
    public function __construct()
    {
        $this->user = Auth::user();
        $this->fechaActual = Carbon::now('America/Argentina/Buenos_Aires');
    }

    public function findBySave($params, $nombre_archivo)
    {
        return ProtesisEntity::create([
            'fecha_emision' => $params->fecha_emision,
            'id_tipo_autorizacion' => $params->id_tipo_autorizacion,
            'id_locatorio' => $params->id_locatorio,
            'dni_afiliado' => $params->dni_afiliado,
            'edad_afiliado' => $params->edad_afiliado,
            'fecha_inicia_prestacion' => $params->fecha_inicia_prestacion,
            'discapacidad' => $params->discapacidad,
            'cod_tipo_diagnostico' => $params->cod_tipo_diagnostico,
            'diagnostico_detallado' => $params->diagnostico_detallado,
            'indicaciones' => $params->indicaciones,
            'cod_provincia' => $params->cod_provincia,
            'cod_medico_solicitante' => $params->cod_medico_solicitante,
            'cod_prestador' => $params->cod_prestador,
            'cod_medico_efector' => $params->cod_medico_efector,
            'via_atencion' => $params->via_atencion,
            'obs_impresion' => $params->obs_impresion,
            'obs_interna' => $params->obs_interna,
            'cod_usuario_registra' => $this->user->cod_usuario,
            'fecha_cirugia' => $params->fecha_cirugia,
            'id_condicion' => $params->id_condicion,
            'id_estado' => $params->id_estado,
            'nombre_archivo' => $nombre_archivo
        ]);
    }

    public function findBySaveDetalle($detalle, $idProtesis)
    {
        foreach ($detalle as $key) {
            ProtesisDetalleEntity::create([
                'id_protesis' => $idProtesis,
                'id_producto' => $key->id_producto,
                'cantidad_solicita' => $key->cantidad_solicita,
                'cobertura' => $key->cobertura,
                'tiene_recupero' => $key->tiene_recupero,
                'observaciones' => $key->observaciones,
                'id_programa_especial' => $key->id_programa_especial,
                'id_origen_material' => $key->id_origen_material,
                'id_tipo_cobertura' => $key->id_tipo_cobertura,
                'pmo' => $key->pmo,
                'coseguro' => $key->coseguro,
            ]);
        }
    }

    public function findByAgregarDetalleArchivos($detalle, $idProtesis)
    {
        foreach ($detalle as $key) {
            DetalleComprobanteProtesis::create([
                'nombre_archivo'=>$key['nombre'],
                'fecha_registra'=>$this->fechaActual,
                'activo'=>'1',
                'id_protesis'=>$idProtesis
            ]);
        }
    }

    public function findByUpdate($params, $id, $archivo)
    {
        $protesis = ProtesisEntity::find($id);
        $protesis->fecha_emision = $params->fecha_emision;
        $protesis->id_tipo_autorizacion = $params->id_tipo_autorizacion;
        $protesis->id_locatorio = $params->id_locatorio;
        $protesis->dni_afiliado = $params->dni_afiliado;
        $protesis->edad_afiliado = $params->edad_afiliado;
        $protesis->fecha_inicia_prestacion = $params->fecha_inicia_prestacion;
        $protesis->discapacidad = $params->discapacidad;
        $protesis->cod_tipo_diagnostico = $params->cod_tipo_diagnostico;
        $protesis->diagnostico_detallado = $params->diagnostico_detallado;
        $protesis->indicaciones = $params->indicaciones;
        $protesis->cod_provincia = $params->cod_provincia;
        $protesis->cod_medico_solicitante = $params->cod_medico_solicitante;
        $protesis->cod_prestador = $params->cod_prestador;
        $protesis->cod_medico_efector = $params->cod_medico_efector;
        $protesis->via_atencion = $params->via_atencion;
        $protesis->obs_impresion = $params->obs_impresion;
        $protesis->obs_interna = $params->obs_interna;
        $protesis->fecha_actualiza = $this->fechaActual;
        $protesis->fecha_cirugia = $params->fecha_cirugia;
        $protesis->id_condicion = $params->id_condicion;
        $protesis->id_estado = $params->id_estado;
        if (!is_null($archivo)) {
            $protesis->nombre_archivo = $archivo;
        }

        $protesis->update();

        return $protesis;
    }

    public function findByUpdateDetalle($detalle, $idProtesis)
    {
        foreach ($detalle as $key) {
            if (!is_null($key->id_detalle)) {
                $detalle =  ProtesisDetalleEntity::find($key->id_detalle);
                $detalle->id_protesis = $idProtesis;
                $detalle->id_producto = $key->id_producto;
                $detalle->cantidad_solicita = $key->cantidad_solicita;
                $detalle->cobertura = $key->cobertura;
                $detalle->tiene_recupero = $key->tiene_recupero;
                $detalle->observaciones = $key->observacione;
                $detalle->id_programa_especial = $key->id_programa_especial;
                $detalle->id_origen_material = $key->id_origen_material;
                $detalle->id_tipo_cobertura = $key->id_tipo_cobertura;
                $detalle->pmo = $key->pmo;
                $detalle->coseguro = $key->coseguro;
                $detalle->update();
            } else {
                ProtesisDetalleEntity::create([
                    'id_protesis' => $idProtesis,
                    'id_producto' => $key->id_producto,
                    'cantidad_solicita' => $key->cantidad_solicita,
                    'cobertura' => $key->cobertura,
                    'tiene_recupero' => $key->tiene_recupero,
                    'observaciones' => $key->observaciones,
                    'id_programa_especial' => $key->id_programa_especial,
                    'id_origen_material' => $key->id_origen_material,
                    'id_tipo_cobertura' => $key->id_tipo_cobertura,
                    'pmo' => $key->pmo,
                    'coseguro' => $key->coseguro,
                ]);
            }
        }
    }

    public function findByDeleteDetalleId($idDetalle)
    {
        $detalle =  ProtesisDetalleEntity::find($idDetalle);
        return $detalle->delete();
    }
    public function findByDeleteProtesis($idProtesis)
    {
        DB::delete("DELETE FROM tb_protesis_detalle WHERE id_protesis = ? ", [$idProtesis]);
        $protesis =  ProtesisEntity::find($idProtesis);
        return $protesis->delete();
    }

    public function findById($id)
    {
        return ProtesisEntity::with(['detalle', 'afiliado', 'estado'])
            ->find($id);
    }

    public function findByUpdateEstado($id, $estado)
    {
        $protesis =  ProtesisEntity::find($id);
        $protesis->id_estado = $estado;
        $protesis->update();
        return $protesis;
    }
}
