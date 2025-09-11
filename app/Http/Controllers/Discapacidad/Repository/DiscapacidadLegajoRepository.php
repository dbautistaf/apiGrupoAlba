<?php

namespace App\Http\Controllers\Discapacidad\Repository;

use App\Models\afiliado\AfiliadoLegajoEntity;
use App\Models\IntegracionDiscapacidadModel;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DiscapacidadLegajoRepository
{
    private $user;
    private $fechaActual;
    public function __construct()
    {
        $this->user = Auth::user();
        $this->fechaActual = Carbon::now('America/Argentina/Buenos_Aires');
    }

    public function findByCrearLegajo($params, $archivo)
    {
        return AfiliadoLegajoEntity::create([
            'id_tipo_discapacidad' => $params->id_tipo_discapacidad,
            'diagnostico' => $params->diagnostico,
            'fecha_certificado' => $params->fecha_certificado,
            'fecha_vto' => $params->fecha_vto,
            'dni_afiliado' => $params->dni_afiliado,
            'certificado' => $params->certificado,
            'url_adjunto' => $archivo,
            'edad_afiliado' => $params->edad_afiliado,
            'fecha_registra' => $this->fechaActual,
            'cod_usuario_registra' => $this->user->cod_usuario
        ]);
    }

    public function findbyUpdate($params, $archivo)
    {
        $legajo = AfiliadoLegajoEntity::find($params->id_legajo);
        $legajo->id_tipo_discapacidad = $params->id_tipo_discapacidad;
        $legajo->diagnostico = $params->diagnostico;
        $legajo->fecha_certificado = $params->fecha_certificado;
        $legajo->fecha_vto = $params->fecha_vto;
        $legajo->dni_afiliado = $params->dni_afiliado;
        $legajo->certificado = $params->certificado;
        $legajo->edad_afiliado = $params->edad_afiliado;
        if (!is_null($archivo)) {
            $legajo->url_adjunto = $params->url_adjunto;
        }
        $legajo->fecha_modifica = $this->fechaActual;
        $legajo->cod_usuario_modifica = $this->user->cod_usuario;
        $legajo->update();
        return $legajo;
    }

    public function findById($id)
    {
        return  AfiliadoLegajoEntity::with(['afiliado', 'tipo'])
            ->find($id);
    }

    public function findByDeleteId($id)
    {
        $legajo =  AfiliadoLegajoEntity::find($id);
        $legajo->estado = 'ELIMINAdo';
        $legajo->update();
        return $legajo;
    }

    public function findByListar($paramas)
    {
        $query = AfiliadoLegajoEntity::with(['afiliado', 'tipo']);
       // $query->whereBetween(DB::raw('DATE(fecha_registra)'), [$paramas->desde, $paramas->hasta]);
        if (!is_null($paramas->search)) {
            $query->whereHas('afiliado', function ($query) use ($paramas) {
                $query->where('dni', 'LIKE', "%$paramas->search%")
                    ->orWhere('apellidos', 'LIKE', "%$paramas->search%")
                    ->orWhere('nombre', 'LIKE', "%$paramas->search%");
            });
        }
        $query->where('estado', 'ACTIVO');

        $query->orderByDesc('id_legajo');

        return $query->get();
    }
}
