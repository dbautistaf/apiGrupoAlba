<?php

namespace App\Http\Controllers\SuperIntendencia\Repository;

use App\Models\SuperIntendencia\AltasRegimenGeneralEntity;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class AltasRegimenGeneralRepository
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
        return AltasRegimenGeneralEntity::create([
            'id' => $row[0],
            'tipo' => $row[1],
            'cuil_titular' => trim($row[2]),
            'nombres' => trim($row[3]),
            'telefono' => trim($row[4]),
            'telefono_2' => $row[5],
            'calle' => $row[6],
            'altura' => $row[7],
            'piso' => $row[8],
            'dpto' => $row[9],
            'extra' => $row[10],
            'codigo_postal' => trim($row[10]),
            'localidad' => trim($row[11]),
            'provincia' => trim($row[12]),
            'cuit_empresa' => $row[13],
            'empresa' => $row[14],
            'obra_social_origen' => trim($row[15]),
            'fecha_vigencia' => Carbon::createFromFormat('d/m/Y', trim($row[16]))->format('Y-m-d'),
            'periodo' => trim($row[17]),
            'fecha_importacion' => Carbon::createFromFormat('d/m/Y', trim($row[18]))->format('Y-m-d'),
            'email' => trim($row[19]),
            'fecha_registra' => $this->fechaActual,
            'cod_usuario_registra' => $this->user->cod_usuario,
        ]);
    }

    public function findByExisteRow($value, $optionPeriodo)
    {
        list($mes, $anio) = explode('/', $optionPeriodo);
        $periodo = $anio . $mes;
        return AltasRegimenGeneralEntity::where('cuil_titular', trim($value))
            ->where('periodo', $periodo)
            ->exists();
    }
}
