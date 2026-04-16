<?php

namespace App\Imports;

use App\Models\Afip\FacturasAfipEntity;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithStartRow;

class FacturasAfipImport implements ToCollection, WithStartRow
{

    public function collection(Collection $rows)
    {
        $user = auth()->user();
        foreach ($rows as $row) {
            if (!is_null($row[0]) && $row[0] != 'Fecha') {
                FacturasAfipEntity::create([
                    'fecha' => $this->transformDate($row[0]),
                    'tipo_comprobante' => $row[1],
                    'punto_venta' => $row[2],
                    'numero_desde' => $row[3],
                    'numero_hasta' => $row[4],
                    'cod_autorizacion' => $row[5],
                    'tipo_doc_receptor' => $row[6],
                    'nro_doc_receptor' => $row[7],
                    'denominacion_receptor' => $row[8],
                    'tipo_cambio' => $row[9],
                    'moneda' => $row[10],
                    'imp_neto_gravado' => str_replace(',', '.', $row[11] ?? 0),
                    'imp_neto_no_gravado' => str_replace(',', '.', $row[12] ?? 0),
                    'imp_op_exentas' => str_replace(',', '.', $row[13] ?? 0),
                    'otros_tributos' => str_replace(',', '.', $row[14] ?? 0),
                    'iva' => str_replace(',', '.', $row[15] ?? 0),
                    'imp_total' => str_replace(',', '.', $row[16] ?? 0),
                    'cod_usuario' => $user->cod_usuario,
                    'fecha_importacion' => Carbon::now()
                ]);
            }

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
