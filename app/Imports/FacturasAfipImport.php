<?php

namespace App\Imports;

use App\Models\Afip\FacturasAfipEntity;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithStartRow;

class FacturasAfipImport implements ToCollection, WithStartRow
{
    public function startRow(): int
    {
        return 3; //  empieza desde fila 3
    }

    public function collection(Collection $rows)
    {
        $user = auth()->user();

        foreach ($rows as $row) {

            //  VALIDAR FILA (evita headers y basura)
            if (
                !isset($row[0]) ||
                trim($row[0]) == '' ||
                strtolower(trim($row[0])) == 'fecha' || // header
                !isset($row[2]) ||
                !is_numeric($row[2]) // tipo comprobante debe ser número
            ) {
                continue;
            }

            FacturasAfipEntity::create([
                'fecha_emision' => $this->transformDate($row[0]),
                'tipo_comprobante' => (int) $row[1],
                'punto_venta' => (int) $row[2],
                'numero_desde' => $row[3],
                'numero_hasta' => $row[4],
                'cod_autorizacion' => $row[5],
                'tipo_doc_emisor' => $row[6],
                'nro_doc_emisor' => $row[7],
                'denominacion_emisor' => $row[8],
                'tipo_doc_receptor' => $row[9],
                'nro_doc_receptor' => $row[10],
                'tipo_cambio' => $row[11],
                'moneda' => $row[12],

                'neto_gravado_iva_0' => $this->toDecimal($row[13] ?? 0),
                'iva_25' => $this->toDecimal($row[14] ?? 0),
                'neto_gravado_iva_25' => $this->toDecimal($row[15] ?? 0),
                'iva_5' => $this->toDecimal($row[16] ?? 0),
                'neto_gravado_iva_5' => $this->toDecimal($row[17] ?? 0),
                'iva_105' => $this->toDecimal($row[18] ?? 0),
                'neto_gravado_iva_105' => $this->toDecimal($row[19] ?? 0),
                'iva_21' => $this->toDecimal($row[20] ?? 0),
                'neto_gravado_iva_21' => $this->toDecimal($row[21] ?? 0),
                'iva_27' => $this->toDecimal($row[22] ?? 0),
                'neto_gravado_iva_27' => $this->toDecimal($row[23] ?? 0),
                'imp_neto_gravado' => $this->toDecimal($row[24] ?? 0),
                'imp_neto_no_gravado' => $this->toDecimal($row[25] ?? 0),
                'imp_op_exentas' => $this->toDecimal($row[26] ?? 0),
                'otros_tributos' => $this->toDecimal($row[27] ?? 0),
                'iva' => $this->toDecimal($row[28] ?? 0),
                'imp_total' => $this->toDecimal($row[29] ?? 0),

                'fecha_importacion' => Carbon::now(),
                'cod_usuario' => $user->cod_usuario,
            ]);
        }
    }

    //  FORMATEAR DECIMALES
    private function toDecimal($value)
    {
        if (!$value) return 0;

        return (float) str_replace(',', '.', $value);
    }

    //  FORMATEAR FECHA
    public function transformDate($fecha)
    {
        if (!$fecha || trim($fecha) == '') {
            return null;
        }

        try {
            // Excel numérico
            if (is_numeric($fecha)) {
                return Carbon::instance(
                    \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($fecha)
                )->format('Y-m-d');
            }

            // formato 01/01/2024
            if (strpos($fecha, '/') !== false) {
                return Carbon::createFromFormat('d/m/Y', $fecha)->format('Y-m-d');
            }

            // formato 01-01-2024
            return Carbon::createFromFormat('d-m-Y', $fecha)->format('Y-m-d');
        } catch (\Exception $e) {
            return null;
        }
    }
}
