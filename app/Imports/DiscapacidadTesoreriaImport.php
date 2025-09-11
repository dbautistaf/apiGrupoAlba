<?php

namespace App\Imports;

use App\Models\Discapacidad\DiscapacidadTesoreriaEntity;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithStartRow;

class DiscapacidadTesoreriaImport implements ToCollection, WithStartRow
{
    public $discaNoEnontradas = [];

    public function collection(Collection $rows)
    {
        $fechaActual = Carbon::now('America/Argentina/Buenos_Aires');
        $user = Auth::user();
        foreach ($rows as $row) {
            $disca =  DB::select(
                "SELECT * FROM vw_discapacidad WHERE
            cuil_prestador = ? AND id_practica = ? AND periodo_prestacion = ? AND num_comprobante = ? AND num_cae_cai = ? ",
                [$row[28], $row[3], $row[22], $row[18], $row[17]]
            );

            if (count($disca) > 0) {
                $val = $disca[0];
                DiscapacidadTesoreriaEntity::create([
                    'id_discapacidad' => $val->id_discapacidad,
                    'cuit_prestador' => $row[28],
                    'cbu' => $row[29],
                    'orden_pago_1' => $row[30],
                    'orden_pago_2' => $row[31],
                    'fecha_transferencia_1' => $row[47],
                    'fecha_transferencia_2' => $row[33],
                    'cheque' => $row[34],
                    'importe_transferido' => $row[35],
                    'retencion_ganancias' => $row[36],
                    'retencion_ingresos_brutos' => $row[37],
                    'otras_retenciones' => $row[38],
                    'importe_aplicado_sss' => $row[39],
                    'fondos_propios_cuenta_discapacidad' => $row[40],
                    'fondos_propios_otra_cuenta' => $row[41],
                    'numero_recibo' => $row[42],
                    'importe_reversion' => $row[43],
                    'importe_devuelto_cuenta_sss' => $row[44],
                    'saldo_no_aplicado' => $row[45],
                    'recupero_fondos_propios' => $row[46],
                    'diferencia' => null,
                    'fecha_proceso' => $fechaActual,
                    'id_usuario' => $user->cod_usuario
                ]);
            } else {
                $this->discaNoEnontradas[] = $row;
            }
        }
    }
    public function startRow(): int
    {
        return 2;
    }
}
