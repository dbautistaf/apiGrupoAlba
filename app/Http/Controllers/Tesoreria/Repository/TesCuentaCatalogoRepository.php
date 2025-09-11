<?php

namespace App\Http\Controllers\Tesoreria\Repository;

use App\Models\Tesoreria\TesEmpresaEntity;
use App\Models\Tesoreria\TesEntidadesBancariasEntity;
use App\Models\Tesoreria\TesTipoCuentasBancariasEntity;
use App\Models\Tesoreria\TesTipoFormasPagoEntity;
use App\Models\Tesoreria\TesTipoMonedasEntity;
use App\Models\Tesoreria\TesTipoTransaccionEntity;
use Illuminate\Support\Facades\Cache;

class TesCuentaCatalogoRepository
{

    public function findByListEntidadesBancarias()
    {
        return Cache::rememberForever("catalog_tes_bancos", function () {
            return TesEntidadesBancariasEntity::where('vigente', '1')
                ->orderBy('descripcion_banco')
                ->get();
        });
    }

    public function findByListTipoCuentas()
    {
        return Cache::rememberForever("catalog_tes_tipo_cuentas", function () {
            return TesTipoCuentasBancariasEntity::where('vigente', '1')
                ->orderBy('descripcion_cuenta')
                ->get();
        });
    }

    public function findByListTipoMoneda()
    {
        return Cache::rememberForever("catalog_tes_tipo_monedas", function () {
            return TesTipoMonedasEntity::where('vigente', '1')
                ->orderBy('descripcion_moneda')
                ->get();
        });
    }

    public function findByListTipoFormaPagos()
    {
        return TesTipoFormasPagoEntity::get();
    }

    public function findByListTipoTransaccion()
    {
        return TesTipoTransaccionEntity::where('vigente', '1')->get();
    }
}
