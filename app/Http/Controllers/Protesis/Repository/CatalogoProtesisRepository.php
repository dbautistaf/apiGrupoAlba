<?php

namespace App\Http\Controllers\Protesis\Repository;

use App\Models\Internaciones\TipoDiagnosticoInternacionEntity;
use App\Models\Protesis\CondicionProtesisEntity;
use App\Models\Protesis\EstadoSolicitudProtesisEntity;
use App\Models\Protesis\OrigenMaterialProtesisEntity;
use App\Models\Protesis\ProgramaEspecialProtesisEntity;
use App\Models\Protesis\TipoCoberturaProtesisEntity;
use Illuminate\Support\Facades\Cache;

class CatalogoProtesisRepository
{

    public function findByTipoCondicionProtesis()
    {
        return Cache::rememberForever("catalog_condicion_protesis", function () {
            return CondicionProtesisEntity::where('vigente', '1')
                ->get();
        });
    }

    public function findByEstadoSolicitudProtesis()
    {
        return Cache::rememberForever("catalog_estado_solic_protesis", function () {
            return EstadoSolicitudProtesisEntity::where('vigente', '1')
                ->get();
        });
    }

    public function findByOrigenMaterialProtesis()
    {
        return Cache::rememberForever("catalog_origen_material_protesis", function () {
            return OrigenMaterialProtesisEntity::where('vigente', '1')
                ->get();
        });
    }

    public function findByProgramaEspecialProtesis()
    {
        return Cache::rememberForever("catalog_programa_especial_protesis", function () {
            return ProgramaEspecialProtesisEntity::where('vigente', '1')
                ->get();
        });
    }

    public function findByTipoCoberturaProtesis()
    {
        return Cache::rememberForever("catalog_tipo_cobertura_protesis", function () {
            return TipoCoberturaProtesisEntity::where('vigente', '1')
                ->get();
        });
    }

    public function findByListTipoDiagnosticoLimit($limit)
    {
        return TipoDiagnosticoInternacionEntity::limit($limit)->get();
    }

    public function findByListTipoDiagnosticoCodigoLikeLimit($codigo, $limit)
    {
        return TipoDiagnosticoInternacionEntity::where('codigo_diagnostico', 'LIKE', $codigo . '%')
            ->limit($limit)->get();
    }

    public function findByListTipoDiagnosticoDescripcionLikeLimit($descripcion, $limit)
    {
        return TipoDiagnosticoInternacionEntity::where('descripcion', 'LIKE', $descripcion . '%')
            ->limit($limit)->get();
    }

    public function findByTipoDiagnosticoId($id_diagnostico)
    {
        return TipoDiagnosticoInternacionEntity::where('cod_tipo_diagnostico', $id_diagnostico)->first();
    }

    public function findBySaveTipoDiagnostico($request)
    {
        if ($request->cod_tipo_diagnostico) {
            $query = TipoDiagnosticoInternacionEntity::where('cod_tipo_diagnostico', $request->cod_tipo_diagnostico)->first();
            $query->descripcion = $request->descripcion;
            $query->vigente = $request->vigente;
            $query->codigo_diagnostico =  $request->codigo_diagnostico;
            $query->id2 =  $request->id2;
            $query->id3 =  $request->id3;
            $query->save();
        } else {
            return TipoDiagnosticoInternacionEntity::create([
                'descripcion' => $request->descripcion,
                'vigente' => $request->vigente,
                'codigo_diagnostico' => $request->codigo_diagnostico,
                'id2' => $request->id2,
                'id3' => $request->id3,
            ]);
        }
    }
}
