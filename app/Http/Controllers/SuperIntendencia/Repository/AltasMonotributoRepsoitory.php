<?php

namespace App\Http\Controllers\SuperIntendencia\Repository;

use App\Models\SuperIntendencia\AdhesionAfipEntity;
use App\Models\SuperIntendencia\AltasMonotributoEntity;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class AltasMonotributoRepsoitory
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
        return AltasMonotributoEntity::create([
            'id' => $row[0],
            'tipo' => trim($row[1]),
            'cuil_titular' => trim($row[2]),
            'nombres' => trim($row[3]),
            'telefono' => trim($row[4]),
            'telefono_2' => trim($row[5]),
            'calle'  => trim($row[6]),
            'altura'  => trim($row[7]),
            'piso'  => trim($row[8]),
            'dpto'  => trim($row[9]),
            'codigo_postal' => trim($row[10]),
            'localidad' => trim($row[11]),
            'provincia' => trim($row[12]),
            'cuit_empresa' => trim($row[13]),
            'empresa' => trim($row[14]),
            'obra_social_origen' => trim($row[15]),
            'fecha_vigencia' => Carbon::createFromFormat('d/m/Y', trim($row[16]))->format('Y-m-d'),
            'periodo' => $row[17],
            'fecha_importacion' => Carbon::createFromFormat('d/m/Y', trim($row[18]))->format('Y-m-d'),
            'email' => trim($row[19]),
            'cod_usuario_registra' => $this->user->cod_usuario,
            'fecha_registra' => $this->fechaActual
        ]);
    }

    public function findByExisteRow($value, $optionPeriodo)
    {
        list($mes, $anio) = explode('/', $optionPeriodo);
        $periodo = $anio . $mes;
        return AltasMonotributoEntity::where('cuil_titular', trim($value))
            ->where('periodo',   $periodo)
            ->exists();
    }
}
