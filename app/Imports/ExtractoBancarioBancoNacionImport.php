<?php

namespace App\Imports;

use App\Models\Tesoreria\TesExtractosBancariosEntity;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Str;

class ExtractoBancarioBancoNacionSheetImport implements ToCollection, WithStartRow
{
    private $user;
    private $fechaActual;

    public $id_entidad_bancaria;
    public $obs;
    public $message = "VALID";

    public function __construct($banco, $obs)
    {
        $this->id_entidad_bancaria = $banco;
        $this->obs = $obs;
        $this->user = Auth::user();
        $this->fechaActual = Carbon::now('America/Argentina/Buenos_Aires');
    }

    public function collection(Collection $rows)
    {
        $nextRow = 1;
        $isProcesar= 0;
        foreach ($rows as $row) {
            if ($nextRow == 1 && $row[0] !== 'Banco Nación') {
                $this->message = "INVALID";
                break;
            }

            if ( $nextRow > 6 && $this->message == "VALID") {
                //Log::info("FILA  => " . $row[0]);
                TesExtractosBancariosEntity::create([
                    'id_entidad_bancaria' => $this->id_entidad_bancaria,
                    'fecha_operacion' => $this->formatFecha($row[0]),
                    'fecha_valor' => $this->formatFecha($row[0]),
                    'concepto' => $row[2],
                    'codigo' => $row[1],
                    'monto_credito' => 0,
                    'monto_debito' => 0,
                    'monto_saldo_parcial' => $this->formatMonto($row[3] ? $row[3] : 0),
                    'monto_saldo_disponible' => $this->formatMonto($row[4] ? $row[4] : 0),
                    'id_usuario' => $this->user->cod_usuario,
                    'fecha_registra' => $this->fechaActual,
                    'observaciones' => $this->obs
                ]);
            }

            $nextRow++;
        }
    }

    public function startRow(): int
    {
        return 1;
    }

    public function formatFecha($fecha)
    {
        if (!$fecha || trim($fecha) == '') {
            return null; // Si está vacía, devuelve null
        }

        try {
            // Si la fecha ya viene en formato numérico (Excel la guarda como timestamp)
            if (is_numeric($fecha)) {
                return Carbon::instance(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($fecha))
                    ->format('Y-m-d');
            }

            // Detectar si el formato es "d/m/Y" en lugar de "d-m-Y"
            if (strpos($fecha, '/') !== false) {
                return Carbon::createFromFormat('d/m/Y', $fecha)->format('Y-m-d');
            }

            // Intentar con el formato esperado "d-m-Y"
            return Carbon::createFromFormat('d-m-Y', $fecha)->format('Y-m-d');
        } catch (\Exception $e) {
            Log::error("Error al convertir fecha: " . $fecha);
            return null;
        }
    }

    public function formatMonto($valor)
    {
        // @Eliminar los puntos (separadores de miles)
        $valor = str_replace('.', '', $valor);

        // @Reemplazar la coma decimal por un punto
        $valor = str_replace(',', '.', $valor);

        // @REMPLAZAR EL $
        $valor = str_replace('$ ', '', $valor);
        return $valor;
    }
}

class ExtractoBancarioBancoNacionImport implements WithMultipleSheets
{
    private $banco;
    private $obs;

    public $message;

    public function __construct($banco, $obs)
    {
        $this->banco = $banco;
        $this->obs = $obs;
    }

    public function sheets(): array
    {
        $sheet = new ExtractoBancarioBancoNacionSheetImport($this->banco, $this->obs);
        $this->message = &$sheet->message;

        return [0 => $sheet];

        /*  return [
             0 => new ExtractoBancarioSheetImport($this->banco, $this->obs)
         ]; */
    }
}
