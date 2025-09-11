<?php

namespace App\Http\Controllers\Discapacidad\Repository;

use App\Models\DiscaPacidadDetalleModel;
use App\Models\IntegracionDiscapacidadModel;
use App\Models\SubsidiosDiscapacidadModel;

class DiscaSubsidioRepository
{

    public function findByPrestacionesListCuilAndPeriodoAndPractica($cuil, $periodo, $practica)
    {
        return DiscaPacidadDetalleModel::with(['practica', 'subsidiodisca', 'disca', 'disca.afiliado'])
            ->whereHas('disca', function ($query) use ($cuil, $periodo) {
                $query->where('cuil_beneficiario', $cuil)
                    ->where('periodo_prestacion', $periodo)
                    ->where('procesado', '0')
                    ->where('cod_usuario', '2');
            })
            ->where('subsidio', 0)
            ->where('id_practica', $practica)
            ->get();
    }

    public function findByUpdateDetalle($idDetalle)
    {
        $update = DiscaPacidadDetalleModel::find($idDetalle);
        $update->subsidio = 1;
        $update->save();
        return $update;
    }

    public function findBySave($numLiquidacion, $importeSolicitado, $importeSubsidio, $fecha, $idDetalle)
    {
        return SubsidiosDiscapacidadModel::create([
            'num_liquidacion' => $numLiquidacion,
            'importe_solicitado' => $importeSolicitado,
            'importe_subsidiado' => $importeSubsidio,
            'fecha_registra' => $fecha,
            'id_discapacidad_detalle' => $idDetalle
        ]);
    }

    public function findByUpdateDisca($numero_liquidacion, $estado, $idDisca)
    {
        $disca = IntegracionDiscapacidadModel::find($idDisca);
        $disca->procesado = $estado;
        $disca->numero_liquidacion = $numero_liquidacion;
        $disca->update();
    }
}
