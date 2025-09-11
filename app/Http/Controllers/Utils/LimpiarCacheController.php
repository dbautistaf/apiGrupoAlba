<?php

namespace App\Http\Controllers\Utils;

use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Cache;

class LimpiarCacheController extends Controller
{

    public function getClearCache()
    {
        Cache::forget("catalog_tipo_sector_deriv");
        Cache::forget("catalog_tipo_paciente_deriv");
        Cache::forget("catalog_tipo_derivacion_deriv");
        Cache::forget("catalog_tipo_traslado_deriv");
        Cache::forget("catalog_tipo_movil_deriv");
        Cache::forget("catalog_egreso_deriv");
        Cache::forget("catalog_requi_extra_deriv");

        Cache::forget("catalog_tipo_prestacion");
        Cache::forget("catalog_tipo_internacion");
        Cache::forget("catalog_tipo_habitacion");
        Cache::forget("catalog_tipo_categoria_internacion");
        Cache::forget("catalog_tipo_facturacion_internacion");
        Cache::forget("catalog_tipo_egreso_internacion");

        Cache::forget("catalog_condicion_protesis");
        Cache::forget("catalog_estado_solic_protesis");
        Cache::forget("catalog_origen_material_protesis");
        Cache::forget("catalog_programa_especial_protesis");
        Cache::forget("catalog_tipo_cobertura_protesis");

        Cache::forget("catalog_domici_tipo_estado");

        Cache::forget("catalog_tes_bancos");
        Cache::forget("catalog_tes_tipo_cuentas");
        Cache::forget("catalog_tes_tipo_monedas");
        Cache::forget('catalog_tes_estado');

        return response()->json([
            'status' => 'success',
            'message' => 'Caches limpiados correctamente.'
        ]);
    }
}
