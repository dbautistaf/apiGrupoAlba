<?php

namespace App\Http\Controllers\Derivacion\Repository;

use App\Models\Derivaciones\TipoDerivacionEntity;
use App\Models\Derivaciones\TipoEgresoEntity;
use App\Models\Derivaciones\TipoMotivoTrasladoEntity;
use App\Models\Derivaciones\TipoMovilEntity;
use App\Models\Derivaciones\TipoPacienteEntity;
use App\Models\Derivaciones\TipoRequisitosExtrasEntity;
use App\Models\Derivaciones\TipoSectorEntity;
use Illuminate\Support\Facades\Cache;

class CatalogoDerivacionesRepository
{

    public function findByTipoSector()
    {
        return Cache::rememberForever("catalog_tipo_sector_deriv", function () {
            return TipoSectorEntity::where('vigente', '1')
                ->get();
        });
    }

    public function findByTipoPaciente()
    {
        return Cache::rememberForever("catalog_tipo_paciente_deriv", function () {
            return TipoPacienteEntity::where('vigente', '1')
                ->get();
        });
    }

    public function findByTipoDerivacion()
    {
        return Cache::rememberForever("catalog_tipo_derivacion_deriv", function () {
            return TipoDerivacionEntity::where('vigente', '1')
                ->get();
        });
    }

    public function findByTipoMotivoTraslado()
    {
        return Cache::rememberForever("catalog_tipo_traslado_deriv", function () {
            return TipoMotivoTrasladoEntity::where('vigente', '1')
                ->get();
        });
    }

    public function findByTipoMovilTraslado()
    {
        return Cache::rememberForever("catalog_tipo_movil_deriv", function () {
            return TipoMovilEntity::where('vigente', '1')
                ->get();
        });
    }

    public function findByTipoEgreso()
    {
        //return Cache::rememberForever("catalog_egreso_deriv", function () {
        return TipoEgresoEntity::where('vigente', '1')
            ->get();
        // });
    }

    public function findByTipoRequisitosExtras()
    {
        return Cache::rememberForever("catalog_requi_extra_deriv", function () {
            return TipoRequisitosExtrasEntity::where('vigente', '1')
                ->get();
        });
    }
}
