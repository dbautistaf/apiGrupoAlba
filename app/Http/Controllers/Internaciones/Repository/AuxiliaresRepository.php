<?php

namespace App\Http\Controllers\Internaciones\Repository;

use App\Models\Internaciones\CategoriaInternacionEntity;
use App\Models\Internaciones\TipoDiagnosticoInternacionEntity;
use App\Models\Internaciones\TipoEgresoInternacionEntity;
use App\Models\Internaciones\TipoFacturacionInternacionEntity;
use App\Models\Internaciones\TipoHabitacionEntity;
use App\Models\Internaciones\TipoInternacionEntity;
use App\Models\Internaciones\TipoPrestacionEntity;
use Illuminate\Support\Facades\Cache;

class AuxiliaresRepository
{
    // php artisan cache:clear

    public function findByTipoPrestacion()
    {
        return Cache::rememberForever("catalog_tipo_prestacion", function () {
            return TipoPrestacionEntity::where('vigente', '1')
                ->get();
        });
    }

    public function findByTipoInternacion()
    {
        /* return TipoInternacionEntity::where('vigente', '1')
            ->get(); */
        return Cache::rememberForever("catalog_tipo_internacion", function () {
            return TipoInternacionEntity::where('vigente', '1')
                ->get();
        });
    }

    public function findByTipoHabitacion()
    {
        /* return TipoHabitacionEntity::where('vigente', '1')
            ->get(); */
        return Cache::rememberForever("catalog_tipo_habitacion", function () {
            return TipoHabitacionEntity::where('vigente', '1')
                ->get();
        });
    }

    public function findByTipoCategoriaInternacion()
    {
        /*  return CategoriaInternacionEntity::where('vigente', '1')
            ->get(); */
        return Cache::rememberForever("catalog_tipo_categoria_internacion", function () {
            return CategoriaInternacionEntity::where('vigente', '1')
                ->get();
        });
    }

    public function findByTipoPacturacionInternacion()
    {
        /* return TipoFacturacionInternacionEntity::where('vigente', '1')
            ->get(); */
        return Cache::rememberForever("catalog_tipo_facturacion_internacion", function () {
            return TipoFacturacionInternacionEntity::where('vigente', '1')
                ->get();
        });
    }

    public function findByTipoEgresoInternacion()
    {
        /* return TipoEgresoInternacionEntity::where('vigente', '1')
            ->get(); */
        return Cache::rememberForever("catalog_tipo_egreso_internacion", function () {
            return TipoEgresoInternacionEntity::where('vigente', '1')
                ->get();
        });
    }

    public function findByTipoDiagnosticoInternacion($search)
    {
        return TipoDiagnosticoInternacionEntity::where('vigente', '1')
            ->where('descripcion', 'LIKE', "$search%")
            ->orderByDesc('cod_tipo_diagnostico')
            ->limit(20)
            ->get();
    }

    public function findByTipoDiagnosticoInternacionLimit($limit)
    {
        return TipoDiagnosticoInternacionEntity::where('vigente', '1')
            ->orderByDesc('cod_tipo_diagnostico')
            ->limit($limit)
            ->get();
    }

    public function findByTipoDiagnosticoInternacionId($id)
    {
        return TipoDiagnosticoInternacionEntity::find($id);
    }

    public function findByCreateDiagnostico($params)
    {
        return TipoDiagnosticoInternacionEntity::create([
            'descripcion' => $params->descripcion
        ]);
    }
}
