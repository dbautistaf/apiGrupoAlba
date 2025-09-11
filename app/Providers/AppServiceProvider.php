<?php

namespace App\Providers;

use App\Models\facturacion\FacturacionDatosEntity;
use App\Models\facturacion\FacturacionDetalleDescuentoEntity;
use App\Models\facturacion\FacturacionDetalleEntity;
use App\Models\facturacion\FacturacionDetalleImpuestoEntity;
use App\Observers\AuditFacturacionDatosObserver;
use App\Observers\AuditFacturacionDescuentosObserver;
use App\Observers\AuditFacturacionDetalleObserver;
use App\Observers\AuditFacturacionImpuestosObserver;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{

    public function register()
    {
        //
    }


    public function boot()
    {
        FacturacionDatosEntity::observe(AuditFacturacionDatosObserver::class);
        FacturacionDetalleDescuentoEntity::observe(AuditFacturacionDescuentosObserver::class);
        FacturacionDetalleEntity::observe(AuditFacturacionDetalleObserver::class);
        FacturacionDetalleImpuestoEntity::observe(AuditFacturacionImpuestosObserver::class);
    }
}
