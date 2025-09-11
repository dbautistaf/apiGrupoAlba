<?php
namespace App\Http\Controllers\Afiliados\Repository;

use App\Models\afiliado\AfiliadoPadronEntity;
use App\Models\afiliado\PadronAfiliadoExternoEntity;
use Illuminate\Support\Facades\Http;

class AfiliadoFatfaRepository
{
    public function findByExistsConvenioFatfa($cuilBeneficiario, $dni)
    {
        $url = 'https://fatfa.site/apifatfa/api/v1/fatfa/verificar-empleado?ndocumento=' . $cuilBeneficiario;
        $response = Http::withOptions([
            'verify' => false,
        ])->get($url);
        $estado = '0';

        if ($response->successful()) {
            $estado = $response->body();
            $this->findByCeheckVerificadoPadronApi($dni, $estado);
            $this->findByCeheckVerificadoPadron($dni, $estado);
        }
        return $estado;
    }

    public function findByCeheckVerificadoPadronApi($dni, $isValue)
    {
        $person = PadronAfiliadoExternoEntity::where('dni', $dni)
            ->first();
        $person->sindical = $isValue;
        $person->verificado = '1';
        $person->update();
        return $person;
    }

    public function findByCeheckVerificadoPadron($dni, $isValue)
    {
        $person = AfiliadoPadronEntity::where('dni', $dni)
            ->first();
        if (!is_null($person)) {
            $person->sindical = $isValue;
            $person->verificado = '1';
            $person->update();
        }

        return $person;
    }
}
