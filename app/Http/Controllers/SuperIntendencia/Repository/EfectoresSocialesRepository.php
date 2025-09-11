<?php

namespace App\Http\Controllers\SuperIntendencia\Repository;

use App\Models\SuperIntendencia\EfectoresSocialesEntity;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class EfectoresSocialesRepository
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
        return EfectoresSocialesEntity::create([
            'cuit_titular' => trim($row[0]),
            'obra_social' => trim($row[1]),
            'nombres_efector' => trim($row[2]),
            'calle' => trim($row[3]),
            'numero' => trim($row[4]),
            'piso' => trim($row[5]),
            'departamento' => trim($row[6]),
            'localidad' => trim($row[7]),
            'codigo_postal' => trim($row[8]),
            'provincia' => trim($row[9]),
            'id_usuario' => $this->user->cod_usuario,
            'periodo_importacion' => $periodo,
            'fecha_importacion' => $this->fechaActual
        ]);
    }

    public function findByExisteRow($value, $periodo)
    {
        return EfectoresSocialesEntity::where('cuit_titular', trim($value))
            ->where('periodo_importacion', $periodo)
            ->exists();
    }
}
