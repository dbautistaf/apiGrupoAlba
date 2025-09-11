<?php

namespace App\Http\Controllers\facturacion\repository;
use App\Http\Controllers\facturacion\dto\TipoComprobantesFacturacionDTO;
use App\Http\Controllers\facturacion\dto\TipoEfectorDTO;
use App\Http\Controllers\facturacion\dto\TipoFacturaDTO;
use App\Http\Controllers\facturacion\dto\TipoFiltroDTO;
use App\Http\Controllers\facturacion\dto\TipoImputacionContableDTO;
use App\Http\Controllers\facturacion\dto\TipoImputacionContableSintetizadaDTO;
use App\Http\Controllers\facturacion\dto\TipoIvaDTO;
use App\Models\facturacion\FacturacionTipoEfectorEntity;
use App\Models\facturacion\FacturacionTipoFiltroEntity;
use App\Models\facturacion\FacturacionTipoImputacionContableEntity;
use App\Models\facturacion\FacturacionTipoImputacionSintetizadaEntity;
use App\Models\facturacion\FacturacionTipoIvaEntity;
use App\Models\facturacion\TipoComprobanteFacturacionEntity;
use App\Models\facturacion\TipoFacturacionEntity;

class AuxiliaresRepository
{

    public function findByListTipoFacturacion()
    {
        return TipoFacturacionEntity::where('vigente', '1')
            ->get()
            ->map(function ($tipo) {
                return new TipoFacturaDTO($tipo->id_tipo_factura, $tipo->descripcion);
            });
    }

    public function findByListTipoComprobantesFacturacion()
    {
        return TipoComprobanteFacturacionEntity::where('vigente', '1')
            ->get()
            ->map(function ($tipo) {
                return new TipoComprobantesFacturacionDTO($tipo->id_tipo_comprobante, $tipo->descripcion);
            });
    }

    public function findByListTipoImputacionContable()
    {
        return FacturacionTipoImputacionContableEntity::where('vigente', '1')
            ->get()
            ->map(function ($tipo) {
                return new TipoImputacionContableDTO($tipo->id_tipo_imputacion, $tipo->descripcion);
            });
    }

    public function findByListTipoTipoIva()
    {
        return FacturacionTipoIvaEntity::where('vigente', '1')
            ->get()
            ->map(function ($tipo) {
                return new TipoIvaDTO($tipo->id_tipo_iva, $tipo->descripcion, $tipo->valor_iva);
            });
    }

    public function findByListTipoFiltro()
    {
        return FacturacionTipoFiltroEntity::where('vigente', '1')
            ->get()
            ->map(function ($tipo) {
                return new TipoFiltroDTO($tipo->id_tipo, $tipo->descripcion);
            });
    }

    public function findByListTipoEfector()
    {
        return FacturacionTipoEfectorEntity::where('vigente', '1')
            ->get()
            ->map(function ($tipo) {
                return new TipoEfectorDTO($tipo->id_tipo_efector, $tipo->descripcion);
            });
    }

    public function findByListTipoImputacionContableSintetizada()
    {
        return FacturacionTipoImputacionSintetizadaEntity::where('vigente', '1')
            ->get()
            ->map(function ($tipo) {
                return new TipoImputacionContableSintetizadaDTO($tipo->id_tipo_imputacion_sintetizada, $tipo->descripcion);
            });
    }

}
