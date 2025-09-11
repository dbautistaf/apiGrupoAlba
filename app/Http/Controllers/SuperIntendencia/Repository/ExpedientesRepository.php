<?php

namespace App\Http\Controllers\SuperIntendencia\Repository;

use App\Models\SuperIntendencia\ExpedientesEntity;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class ExpedientesRepository
{

    private $user;
    private $fechaActual;
    public function __construct()
    {
        $this->user = Auth::user();
        $this->fechaActual = Carbon::now('America/Argentina/Buenos_Aires');
    }

    public function findByCrearRow($row, $periodo)
    {
        return ExpedientesEntity::create([
            'rnos' => trim($row[0]),
            'cuil_tit' => trim($row[1]),
            'nombres' => trim($row[2]),
            'cod_mov' => trim($row[3]),
            'movimiento' => trim($row[4]),
            'fecha_vigencia' => trim($row[5]),
            'expediente' => trim($row[6]),
            'año_expediente' => trim($row[7]),
            'tipo_disposicion' => trim($row[8]),
            'disposicion' => trim($row[9]),
            'periodo' => $periodo,
            'id_usuario' => $this->user->cod_usuario,
            'fecha_importacion' => $this->fechaActual
        ]);
    }

    public function findByExisteRow($value, $periodo)
    {
        return ExpedientesEntity::where('cuil_tit', trim($value))
            ->where('periodo',  $periodo)
            ->first();
    }
}
