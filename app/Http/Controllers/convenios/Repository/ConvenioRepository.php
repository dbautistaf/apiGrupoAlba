<?php

namespace App\Http\Controllers\convenios\Repository;

use App\Models\convenios\ConveniosEntity;
use App\Models\convenios\ConveniosPrestadoresEntity;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class ConvenioRepository
{

    private $fechaActual;
    private $user;
    public function __construct()
    {
        $this->fechaActual = Carbon::now('America/Argentina/Buenos_Aires');
        $this->user = Auth::user();
    }


    public function findByConvenioPrestador($id)
    {
        $prestadorConv = null;
        $contrato = null;

        if (ConveniosPrestadoresEntity::where('cod_prestador', $id)->exists()) {
            $prestadorConv = ConveniosPrestadoresEntity::where('cod_prestador', $id)->first();

            $contrato = ConveniosEntity::with([
                'categoriaPagos',
                'tipoValorizacion',
                'altasCategorias',
                'tipoPlanes'
            ])
                ->where('cod_convenio', $prestadorConv->cod_convenio)
                ->where('vigente', '1')->first();
            $contrato->existe = true;
        } else {
            $contrato = ["existe" => false];
        }

        return $contrato;
    }

    public function findByListFiltrarConvenios($param)
    {
        
        $query = ConveniosEntity::with(['provincia','locatarios'])
        ->orderByDesc('fecha_inicio');
        
        if(!empty($param->search)){
            $query->where('descripcion_convenio', 'LIKE', "%{$param->search}%");
        }
        if(!empty($param->desde) && !empty($param->hasta)){
            $query->whereBetween('fecha_registra', [$param->desde, $param->hasta]);
        }
        return $query->get();
    }

    public function findByIdPricipal($id)
    {
        return ConveniosEntity::with([
            'locatarios',
            'provincia',
            'localidades',
            'categoriaPagos',
            'tipoValorizacion',
            'altasCategorias',
            'tipoPlanes',
            'tipoCoberturas',
            'origen'
        ])
            ->find($id);
    }

    public function findBysave($params)
    {
        return ConveniosEntity::create([
            'descripcion_convenio' => $params->descripcion_convenio,
            'fecha_inicio' => $params->fecha_inicio,
            'fecha_fin' => $params->fecha_fin,
            'cod_provincia' => $params->cod_provincia,
            'vigente' => $params->vigente,
            'cod_usuario_registra' => $this->user->cod_usuario,
            'fecha_registra' => $this->fechaActual,
            'posee_coseguro' => $params->posee_coseguro
        ]);
    }

    public function findBysaveId($params)
    {
        $convenio = ConveniosEntity::find($params->cod_convenio);
        $convenio->descripcion_convenio = $params->descripcion_convenio;
        $convenio->fecha_inicio = $params->fecha_inicio;
        $convenio->fecha_fin = $params->fecha_fin;
        $convenio->cod_provincia = $params->cod_provincia;
        $convenio->vigente = $params->vigente;
        $convenio->posee_coseguro = $params->posee_coseguro;
        $convenio->update();
        return $convenio;
    }
}
