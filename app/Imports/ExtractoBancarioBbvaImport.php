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

class ExtractoBancarioSheetImport implements ToCollection, WithStartRow
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
        $nextRow = 2;
        $cuenta = "";
        $sucursal = "";
        $saldo = 0;
        foreach ($rows as $row) {
            if ($nextRow == 2 && !Str::startsWith($row[0], "Cuenta:")) {
                $this->message = "INVALID";
                break;
            }
            if ($nextRow == 2) {
                $cuenta = $row[1];
            }
            if ($nextRow == 3) {
                $sucursal = $row[1];
            }
            if ($nextRow == 4) {
                $saldo = $row[1];
            }

            if ($nextRow >= 8 && $this->message == "VALID") {
                TesExtractosBancariosEntity::create([
                    'id_entidad_bancaria' => $this->id_entidad_bancaria,
                    'fecha_operacion' => $this->formatFecha($row[0]),
                    'fecha_valor' => $this->formatFecha($row[1]),
                    'concepto' => $row[2],
                    'codigo' => $row[3],
                    'num_cheque' => $row[4],
                    'oficina' => $row[5],
                    'monto_credito' => str_replace(',', '', $row[6] ? $row[6] : 0),
                    'monto_debito' => str_replace(',', '', $row[7] ? $row[7] : 0),
                    'monto_saldo_parcial' => str_replace(',', '', $row[8] ? $row[8] : 0),
                    'monto_saldo_disponible' => str_replace(',', '', $saldo ? $saldo : 0),
                    'id_usuario' => $this->user->cod_usuario,
                    'fecha_registra' => $this->fechaActual,
                    'observaciones' => $this->obs,
                    'detalle' => "Cuenta: [$cuenta] \n Sucursal: [$sucursal]"
                ]);
            }

            $nextRow++;
        }
    }

    public function startRow(): int
    {
        return 2;
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
}

class ExtractoBancarioBbvaImport implements WithMultipleSheets
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
        $sheet = new ExtractoBancarioSheetImport($this->banco, $this->obs);
        $this->message = &$sheet->message;

        return [0 => $sheet];

        /*  return [
             0 => new ExtractoBancarioSheetImport($this->banco, $this->obs)
         ]; */
    }
}
