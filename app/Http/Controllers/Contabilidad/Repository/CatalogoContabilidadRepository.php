<?php

namespace App\Http\Controllers\Contabilidad\Repository;

use App\Models\Contabilidad\NivelesPlanCuentaEntity;
use App\Models\Contabilidad\TipoPlanCuentaEntity;
use App\Models\Contabilidad\TipoPlanOrganicoCuentaEntity;
use App\Models\Contabilidad\TipoRetencionesEntity;

class CatalogoContabilidadRepository
{

    public function findByListarTipoPlanCuenta()
    {
        return TipoPlanCuentaEntity::get();
    }

    public function findByListarNiveles()
    {
        return NivelesPlanCuentaEntity::get();
    }

    public function findByListarTipoPlanOrganicoCuenta()
    {
        return TipoPlanOrganicoCuentaEntity::get();
    }

    public function findByListTipoRetencion()
    {
        return TipoRetencionesEntity::get();
    }
}
