<?php

namespace App\Imports;

use App\Models\Tesoreria\TesExtractosBancariosEntity;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\WithStartRow;

class ExtractoBancarioCygnusSheetImport implements ToCollection, WithStartRow
{
    private $user;
    private $fechaActual;
    public $id_entidad_bancaria;
    public $obs;
    public $message = 'VALID';
    public $id_locatario;

    public function __construct($banco, $obs, $id_locatario)
    {
        $this->id_entidad_bancaria = $banco;
        $this->obs = $obs;
        $this->user = Auth::user();
        $this->fechaActual = Carbon::now('America/Argentina/Buenos_Aires');
        $this->id_locatario = $id_locatario;
    }

    public function collection(Collection $rows)
    {
        $nextRow = 1;
        foreach ($rows as $row) {
            // Saltamos encabezados o filas vacías si StartRow no lo atrapa
            if ($nextRow == 1 && (strtoupper(trim($row[0])) == 'FECHA' || strtoupper(trim($row[1])) == 'BANCO')) {
                $nextRow++;
                continue;
            }

            if ($this->message == 'VALID' && !empty($row[0])) {
                try {
                    TesExtractosBancariosEntity::create([
                        'id_entidad_bancaria' => $this->id_entidad_bancaria,  // Usamos el seleccionado en el frontend
                        'fecha' => $this->formatFecha($row[0]),
                        'banco'=>$row[1] ?? '-',
                        'concepto' => $row[2] ?? '-',  // Columna C
                        'importe' => $this->formatMonto($row[3] ?? 0),  // Columna D
                        'saldo' => $this->formatMonto($row[4] ?? 0),  // Columna E
                        'referencia' => $row[5] ?? null,  // Columna F
                        'detalle' => $row[6] ?? null,  // Columna G
                        'estado_conciliacion' => 'PENDIENTE',
                        'score_matching' => null,
                        'id_usuario' => $this->user ? $this->user->cod_usuario : 1,
                        'fecha_registra' => $this->fechaActual,
                        'observaciones' => $this->obs,
                        'id_locatario' => $this->id_locatario
                    ]);
                } catch (\Exception $e) {
                    Log::error('Error importando fila ' . $nextRow . ': ' . $e->getMessage());
                }
            }

            $nextRow++;
        }
    }

    public function startRow(): int
    {
        return 2;  // Empezar en la fila 2 (asumiendo que la 1 son los encabezados)
    }

    public function formatFecha($fecha)
    {
        if (!$fecha || trim($fecha) == '') {
            return null;
        }
        try {
            if (is_numeric($fecha)) {
                return Carbon::instance(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($fecha))->format('Y-m-d');
            }
            if (strpos($fecha, '/') !== false) {
                return Carbon::createFromFormat('d/m/Y', $fecha)->format('Y-m-d');
            }
            if (strpos($fecha, '-') !== false) {
                // Intentar varios formatos con guion
                return Carbon::parse($fecha)->format('Y-m-d');
            }
            return Carbon::parse($fecha)->format('Y-m-d');
        } catch (\Exception $e) {
            Log::error('Error al convertir fecha: ' . $fecha);
            return null;
        }
    }

    public function formatMonto($valor)
    {
        if (!$valor)
            return 0;

        if (is_numeric($valor)) {
            return $valor;
        }

        // Limpiar formato string (Ej: "$ 1.500,50")
        $valor = str_replace('$', '', $valor);
        $valor = trim($valor);

        // Si tiene punto de miles y coma decimal
        if (strpos($valor, '.') !== false && strpos($valor, ',') !== false) {
            $valor = str_replace('.', '', $valor);
            $valor = str_replace(',', '.', $valor);
        } elseif (strpos($valor, ',') !== false) {
            // Si solo tiene coma, asumimos que es decimal
            $valor = str_replace(',', '.', $valor);
        }

        return (float) $valor;
    }
}

class ExtractoBancarioCygnusImport implements WithMultipleSheets
{
    private $banco;
    private $obs;
    private $id_locatario;
    public $message;

    public function __construct($banco, $obs, $id_locatario)
    {
        $this->banco = $banco;
        $this->obs = $obs;
        $this->id_locatario = $id_locatario;
    }

    public function sheets(): array
    {
        $sheet = new ExtractoBancarioCygnusSheetImport($this->banco, $this->obs, $this->id_locatario);
        $this->message = &$sheet->message;
        return [0 => $sheet];
    }
}
