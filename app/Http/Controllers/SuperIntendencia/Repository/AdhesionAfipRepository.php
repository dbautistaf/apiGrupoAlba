<?php

namespace App\Http\Controllers\SuperIntendencia\Repository;

use App\Models\SuperIntendencia\AdhesionAfipEntity;
use App\Models\SuperIntendencia\AltasRegimenGeneralEntity;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class AdhesionAfipRepository
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
        return AdhesionAfipEntity::create([
            'cuil_tit' => trim($row[0]),
            'rnos' => trim($row[1]),
            'periodo' => trim($row[2]),
            'cuit' => trim($row[3]),
            'nombres' => trim($row[4]),
            'calle' => trim($row[5]),
            'numero' => trim($row[6]),
            'piso' => trim($row[7]),
            'depto' => trim($row[8]),
            'localidad' => trim($row[9]),
            'periodo_import' => $periodo,
            'id_usuario' => $this->user->cod_usuario,
            'fecha_importacion' => $this->fechaActual
        ]);
    }

    public function findByExisteRow($value, $periodo)
    {
        return AdhesionAfipEntity::where('cuil_tit', trim($value))
            ->where('periodo_import', '=', $periodo)
            ->exists();
    }
}
