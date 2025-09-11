<?php

namespace App\Http\Controllers\SuperIntendencia\Repository;

use App\Models\SuperIntendencia\FamiliaresMonotributoEntity;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class FamiliaresMonotributoRepository
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
        $textoUtf8 = mb_convert_encoding($row[4], 'UTF-8', 'ISO-8859-1');

        return FamiliaresMonotributoEntity::create([
            'obra_social' => trim($$row[0]),
            'cuit_titular' => trim($$row[1]),
            'tipo_documento_fam' => trim($$row[2]),
            'nro_documento_fam' => trim($$row[3]),
            'apellido_fam' => trim($textoUtf8),
            'nombres_fam' => trim($$row[5]),
            'parentesco_fam' => trim($$row[6]),
            'fecha_alta_fam' => trim($$row[7]),
            'id_usuario' => $this->user->cod_usuario,
            'periodo_importacion' => $periodo,
            'fecha_importacion' => $this->fechaActual
        ]);
    }

    public function findByExisteRow($value, $periodo)
    {
        return FamiliaresMonotributoEntity::where('nro_documento_fam', $value)
            ->where('periodo_importacion', '=', $periodo)
            ->exists();
    }
}
