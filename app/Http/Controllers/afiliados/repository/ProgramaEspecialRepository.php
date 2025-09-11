<?php

namespace App\Http\Controllers\afiliados\repository;

use App\Models\afiliado\ProgramaEspecialAfiEntity;
use App\Models\afiliado\TipoProgramaEspecialAfiEntity;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class ProgramaEspecialRepository
{

    protected $user;
    protected $fecha;

    public function __construct()
    {
        $this->user = Auth::user();
        $this->fecha = Carbon::now();
    }

    public function findByListarTipos()
    {
        return TipoProgramaEspecialAfiEntity::where('vigente', '1')->get();
    }

    public function findByCrear($params, $fileDocumentacion)
    {
        return ProgramaEspecialAfiEntity::create([
            'dni_afiliado' => $params->dni_afiliado,
            'id_tipo_programa_especial' => $params->id_tipo_programa_especial,
            'fecha_alta' => $params->fecha_alta,
            'fecha_baja' => !empty($params->fecha_baja) ? $params->fecha_baja : NULL,
            'estado_clinico' => $params->estado_clinico,
            'medico_tratante' => $params->medico_tratante,
            'medico_especialidad' => $params->medico_especialidad,
            'medico_telefono' => $params->medico_telefono,
            'medico_email' => $params->medico_email,
            'observaciones' => $params->observaciones,
            'documento_adjunto' => $fileDocumentacion,
            'fecha_registra' => $this->fecha,
            'cod_usuario_registra' => $this->user->cod_usuario,
            'estado_tramite' => $params->estado_tramite,
        ]);
    }

    public function findByUpdate($params, $fileDocumentacion)
    {
        $programa = ProgramaEspecialAfiEntity::find($params->id_programa);
        $programa->dni_afiliado = $params->dni_afiliado;
        $programa->id_tipo_programa_especial = $params->id_tipo_programa_especial;
        $programa->fecha_alta = $params->fecha_alta;
        $programa->fecha_baja = !empty($params->fecha_baja) ? $params->fecha_baja : NULL;
        $programa->estado_clinico = $params->estado_clinico;
        $programa->medico_tratante = $params->medico_tratante;
        $programa->medico_especialidad = $params->medico_especialidad;
        $programa->medico_telefono = $params->medico_telefono;
        $programa->medico_email = $params->medico_email;
        $programa->observaciones = $params->observaciones;
        $programa->documento_adjunto = $fileDocumentacion ?? $params->documento_adjunto;
        $programa->fecha_modifica = $this->fecha;
        $programa->cod_usuario_modifica = $this->user->cod_usuario;
        $programa->estado_tramite = $params->estado_tramite;
        $programa->update();
    }

    public function findById($id)
    {
        return ProgramaEspecialAfiEntity::find($id);
    }

    public function findByListar($filter)
    {
        $sql = ProgramaEspecialAfiEntity::with(['tipo_programa', 'afiliado.obrasocial','afiliado.localidad','afiliado.origen']);

        if (!is_null($filter->search)) {
            $sql->where('dni_afiliado', 'LIKE', "$filter->search%");
            /*  $sql->whereHas('afiliado', function ($jquery) use ($filter) {
                $jquery->where('dni', 'LIKE', ["$filter->search%"])
                    ->orWhere('nombre', 'LIKE', ["$filter->search%"])
                    ->orWhere('apellidos', 'LIKE', ["$filter->search%"]);
            }); */
        }

        if (!is_null($filter->medico_tratante)) {
            $sql->where('medico_tratante', 'LIKE', "$filter->medico_tratante%");
        }

        if (!is_null($filter->fecha_alta)) {
            $sql->where('fecha_alta',   [$filter->fecha_alta]);
        }

        if (!is_null($filter->id_tipo_programa)) {
            $sql->where('id_tipo_programa_especial',   [$filter->id_tipo_programa]);
        }

        $sql->orderByDesc('id_programa');
        $data = $sql->get();

        return $data;
    }

    public function findByIdDelete($id)
    {
        return ProgramaEspecialAfiEntity::find($id)->delete();
    }

}
