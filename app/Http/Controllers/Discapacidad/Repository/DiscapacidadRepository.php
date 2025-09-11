<?php

namespace App\Http\Controllers\Discapacidad\Repository;

use App\Models\DiscaPacidadDetalleModel;
use App\Models\IntegracionDiscapacidadModel;

class DiscapacidadRepository
{

    public function findBySaveDetalle($params, $id_discapacidad)
    {
        return DiscaPacidadDetalleModel::create([
            'id_practica' => $params["id_practica"],
            'cantidad' => $params["cantidad"],
            'dependencia' => $params["dependencia"],
            'id_discapacidad' => $id_discapacidad,
        ]);
    }

    public function findByExistsBoleta($params)
    {
        return IntegracionDiscapacidadModel::where('cuil_beneficiario', $params->cuil_beneficiario)
            ->where('periodo_prestacion', $params->periodo_prestacion)
            ->where('num_cae_cai', $params->num_cae_cai)
            ->where('num_comprobante', $params->num_comprobante)->exists();
    }
}
