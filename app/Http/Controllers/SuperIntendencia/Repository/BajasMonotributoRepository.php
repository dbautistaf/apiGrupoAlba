<?php

namespace App\Http\Controllers\SuperIntendencia\Repository;

use App\Models\SuperIntendencia\BajasMonotributoEntity;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class BajasMonotributoRepository
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
        return BajasMonotributoEntity::create([
            'id' => trim($row[0]),
            'formulario' => trim($row[1]),
            'cuil_titular' => trim($row[2]),
            'nombres' => trim($row[3]),
            'telefono1' => trim($row[4]),
            'telefono2' => trim($row[5]),
            'calle' => trim($row[6]),
            'altura' => trim($row[7]),
            'piso' => trim($row[8]),
            'departamento' => trim($row[9]),
            'codigo_postal' => trim($row[10]),
            'localidad' => trim($row[11]),
            'provincia' => trim($row[12]),
            'obra_social_elegida' => trim($row[13]),
            'fecha_vigencia' => Carbon::createFromFormat('d/m/Y', trim($row[14]))->format('Y-m-d'),
            'periodo' => $row[15],
            'fecha_importacion' => Carbon::createFromFormat('d/m/Y', trim($row[16]))->format('Y-m-d'),
            'email' => trim($row[17]),
            'campo_1' => trim($row[18]),
            'campo_2' => trim($row[19]),
            'campo_3' => trim($row[20]),
            'cod_usuario_registra' => $this->user->cod_usuario,
            'fecha_registra' => $this->fechaActual
        ]);
    }

    public function findByExisteRow($value, $optionPeriodo)
    {
        list($mes, $anio) = explode('/', $optionPeriodo);
        $periodo = $anio . $mes;
        return BajasMonotributoEntity::where('cuil_titular', trim($value))
            ->where('periodo',   $periodo)
            ->exists();
    }
}
