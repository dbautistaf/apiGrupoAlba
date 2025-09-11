<?php

namespace App\Imports;

use App\Models\Afip\ComprobantesAfipCompraEntity;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithStartRow;

class ComprobantesAfipCompraImport implements ToCollection, WithStartRow
{

    public function collection(Collection $rows)
    {
        $user = auth()->user();
        foreach ($rows as $row) {
            ComprobantesAfipCompraEntity::create([
                'fecha_emision' => $this->transformDate($row[0]),
                'tipo_comprobante' => $row[1],
                'punto_venta' => $row[2],
                'numero_comprobante' => $row[3],
                'tipo_doc_vendedor' => $row[4],
                'nro_doc_vendedor' => $row[5],
                'denominacion_vendedor' => $row[6],
                'importe_total_original' => str_replace(',', '.', $row[7] ?? 0),
                'moneda_original' => $row[8],
                'tipo_cambio' => $row[9],
                'importe_no_gravado' => str_replace(',', '.', $row[10] ?? 0),
                'importe_externo' => str_replace(',', '.', $row[11] ?? 0),
                'credito_fiscal_computable' => $row[12] ?? 0,
                'neto_gravado_iva_5' => str_replace(',', '.', $row[22] ?? 0),
                'importe_iva_5' => str_replace(',', '.', $row[23] ?? 0),
                'neto_gravado_iva_10_5' => str_replace(',', '.', $row[24] ?? 0),
                'importe_iva_10_5' => str_replace(',', '.', $row[25] ?? 0),
                'neto_gravado_iva_21' => str_replace(',', '.', $row[26] ?? 0),
                'importe_iva_21' => str_replace(',', '.', $row[27] ?? 0),
                'neto_gravado_iva_27' => str_replace(',', '.', $row[28] ?? 0),
                'importe_iva_27' => str_replace(',', '.', $row[29] ?? 0),
                'total_neto_gravado' => str_replace(',', '.', $row[30] ?? 0),
                'total_iva' => str_replace(',', '.', $row[31] ?? 0),
                'cod_usuario' => $user->cod_usuario,
                'id_locatario' => 1
            ]);
        }
    }
    public function startRow(): int
    {
        return 2;
    }
    public function transformDate($fecha)
    {
        if (!$fecha || trim($fecha) == '') {
            return null;
        }

        try {
            if (is_numeric($fecha)) {
                return Carbon::instance(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($fecha))
                    ->format('Y-m-d');
            }

            if (strpos($fecha, '/') !== false) {
                return Carbon::createFromFormat('d/m/Y', $fecha)->format('Y-m-d');
            }

            return Carbon::createFromFormat('d-m-Y', $fecha)->format('Y-m-d');
        } catch (\Exception $e) {
            return null;
        }
    }
}
