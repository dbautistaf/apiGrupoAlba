<?php

namespace App\Http\Controllers\SuperIntendencia\Repository;

use App\Models\SuperIntendencia\SuperPadronEntity;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class SuperPadronRepository
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
        return SuperPadronEntity::create([
            'rnos' => trim($row[0]),
            'cuit' => trim($row[1]),
            'cuil_tit' => trim($row[2]),
            'parentesco' => trim($row[3]),
            'cuil_benef' => trim($row[4]),
            'tipo_doc' => trim($row[5]),
            'dni' => trim($row[6]),
            'nombres' => trim($row[7]),
            'sexo' => trim($row[8]),
            'estado_civi' => trim($row[9]),
            'fe_nac' => trim($row[10]),
            'nacionalidad' => trim($row[11]),
            'calle' => trim($row[12]),
            'numero' => trim($row[13]),
            'piso' => trim($row[14]),
            'depto' => trim($row[15]),
            'localidad' => trim($row[16]),
            'cp' => trim($row[17]),
            'id_prov' => trim($row[18]),
            'sd2' => trim($row[19]),
            'telefono' => trim($row[20]),
            'sd3' => trim($row[21]),
            'incapacidad' => trim($row[22]),
            'sd5' => trim($row[23]),
            'fe_alta' => trim($row[24]),
            'fe_novedad' => trim($row[25]),
            'periodo' => $periodo,
            'id_usuario' => $this->user->cod_usuario,
            'fecha_importacion' => $this->fechaActual,
        ]);
    }

    public function findByExisteRow($value, $periodo)
    {
        return SuperPadronEntity::where('dni', trim($value))
            ->where('periodo',   $periodo)
            ->first();
    }
}
