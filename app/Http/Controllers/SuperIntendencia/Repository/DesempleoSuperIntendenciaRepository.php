<?php

namespace App\Http\Controllers\SuperIntendencia\Repository;

use App\Models\SuperIntendencia\DesempleoSuperIntendenciaEntity;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class DesempleoSuperIntendenciaRepository
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
        return DesempleoSuperIntendenciaEntity::create([
            'clave_desempleo' => trim($row[0]),
            'marca_fin_pago' => trim($row[1]),
            'parentesco' => trim($row[2]),
            'tipo_documento' => trim($row[3]),
            'nro_documento' => trim($row[4]),
            'provincia' => trim($row[5]),
            'cuil' => trim($row[6]),
            'fecha_nacimiento' => trim($row[7]),
            'nombres' => trim($row[8]),
            'fecha_vigencia' => trim($row[9]),
            'sexo' => trim($row[10]),
            'fecha_inicio_relacion' => trim($row[11]),
            'fecha_cese' => trim($row[12]),
            'rnos' => trim($row[13]),
            'fecha_proceso' => trim($row[14]),
            'cuil_titular' => trim($row[15]),
            'periodo_importacion' => $periodo,
            'id_usuario' => $this->user->cod_usuario,
            'fecha_importacion' => $this->fechaActual,
        ]);
    }

    public function findByExisteRow($value, $periodo)
    {
        return DesempleoSuperIntendenciaEntity::where('nro_documento', trim($value))
            ->where('periodo_importacion', $periodo)
            ->ex();
    }
}
