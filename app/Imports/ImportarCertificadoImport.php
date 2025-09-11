<?php

namespace App\Imports;

use App\Models\afiliado\AfiliadoCertificadoEntity;
use App\Models\afiliado\AfiliadoPadronEntity;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithStartRow;

class ImportarCertificadoImport implements ToCollection, WithStartRow
{

    public function collection(Collection $rows)
    {
        $fechaActual = Carbon::now('America/Argentina/Buenos_Aires');

        foreach ($rows as $row) {
            if ($row[0] !== null) {
                $afiliado = AfiliadoPadronEntity::where('cuil_benef', $row[0])->first();

                $fechaVencimiento = $fechaActual;
                $fechaFormateada = $fechaActual;

                if ($row[3] !== null) {
                    $fechaVencimiento = \DateTime::createFromFormat('d/m/Y', $row[3]);
                    $fechaFormateada = $fechaVencimiento->format('Y-m-d');
                }

                AfiliadoCertificadoEntity::create([
                    'id_tipo_discapacidad' => 1,
                    'diagnostico' => '',
                    'fecha_certificado' => $fechaActual,
                    'fecha_vto' => $fechaFormateada,
                    'id_padron' => $afiliado == null ? null : $afiliado->id,
                    'certificado' => $row[2]
                ]);
            }

        }
    }

    public function startRow(): int
    {
        return 2;
    }
}
