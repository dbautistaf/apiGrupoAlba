<?php

namespace App\Imports;

use App\Models\Tesoreria\TesExtractosBancariosEntity;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\WithStartRow;

class ExtractoBancarioMacroSheetImport implements ToCollection, WithStartRow
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
        $tipo = "";
        $numero = "";
        $moneda = "";
        foreach ($rows as $row) {
            if ($nextRow == 1 && $row[0] !== 'Últimos Movimientos') {
                $this->message = "INVALID";
                break;
            }

            if ($nextRow == 5 && $this->message == "VALID") {
                $tipo = "Tipo: [$row[1]]";
            }
            if ($nextRow == 6 && $this->message == "VALID") {
                $numero = "Numero: [$row[1]]";
            }
            if ($nextRow == 7 && $this->message == "VALID") {
                $moneda = "Moneda: [$row[1]]";
            }

            if (
                $nextRow >= 9 && $this->message == "VALID"
                && !Str::startsWith($row[0], "Fecha de descarga")
                && !Str::startsWith($row[0], "Empresa:")
                && !Str::startsWith($row[0], "Operador:")
            ) {
                //Log::info("FILA  => $nextRow COTNIDO:  " . $row[0]);
                TesExtractosBancariosEntity::create([
                    'id_entidad_bancaria' => $this->id_entidad_bancaria,
                    'fecha_operacion' => $this->formatFecha($row[0]),
                    'fecha_valor' => $this->formatFecha($row[0]),
                    'concepto' => $row[5],
                    'codigo' => $row[4],
                    'num_documento' => $row[3],
                    'monto_credito' => 0,
                    'monto_debito' => 0,
                    'monto_saldo_parcial' => $this->formatMonto($row[6] ? $row[6] : 0),
                    'monto_saldo_disponible' => $this->formatMonto($row[10] ? $row[10] : 0),
                    'id_usuario' => $this->user->cod_usuario,
                    'fecha_registra' => $this->fechaActual,
                    'observaciones' => $this->obs,
                    'detalle' => "$tipo\n $numero\n $moneda\n"
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
        // @Reemplazar la coma decimal por un punto
        $valor = str_replace(',', '', $valor);

        // @REMPLAZAR EL $
        $valor = str_replace('$ ', '', $valor);
        return $valor;
    }
}

class ExtractoBancarioMacroImport implements WithMultipleSheets
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
        $sheet = new ExtractoBancarioMacroSheetImport($this->banco, $this->obs);
        $this->message = &$sheet->message;

        return [0 => $sheet];
    }
}
