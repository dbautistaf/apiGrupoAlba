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
        $fechaActual = Carbon::now('America/Argentina/Buenos_Aires');
        $rows = $rows->slice(2)->values();
        $data = [];
        foreach ($rows as $row) {
            $data[] = [
                'fecha' => $this->transformDate($row[0]),
                'tipo_comprobante' => $row[1],
                'punto_venta' => $row[2],
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

                'fecha_importacion' => $fechaActual,
                'cod_usuario' => $user->cod_usuario,
            ];
        }
        foreach (array_chunk($data, 2000) as $chunk) {
            ComprobantesAfipCompraEntity::insert($chunk);
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

    //  FORMATEAR DECIMALES
    private function toDecimal($value)
    {
        if (!$value) return 0;

        return (float) str_replace(',', '.', $value);
    }

}
