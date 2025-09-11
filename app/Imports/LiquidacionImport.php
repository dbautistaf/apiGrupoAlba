<?php

namespace App\Imports;

use App\Models\liquidacion\LiquidacionObrasSociales;
use App\Models\liquidacion\LiquidacionOsceara;
use App\Models\liquidacion\LiquidacionOsetya;
use App\Models\liquidacion\LiquidacionOsfotModel;
use App\Models\liquidacion\LiquidacionOsmitaModel;
use App\Models\liquidacion\LiquidacionOstvModel;
use App\Models\liquidacion\LiquidacionOsycModel;
use App\Models\liquidacion\LiquidacionPrensa;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithStartRow;

class LiquidacionImport implements ToCollection, WithStartRow
{
    /**
     * @param Collection $collection
     */

    protected $tipo_archivo;
    private $mensaje;

    public function __construct($tipo_archivo)
    {
        $this->tipo_archivo = $tipo_archivo;
    }

    public function getMensaje()
    {
        return $this->mensaje;
    }

    public function collection(Collection $rows)
    {
        $fechaActual = Carbon::now('America/Argentina/Buenos_Aires');
        //
        //try {
        //DB::beginTransaction();
        if ($this->tipo_archivo == 3) {
            foreach ($rows as $row) {
                LiquidacionOsfotModel::create([
                    'CONVENIO' => $row[0],
                    'FILIAL' => $row[1],
                    'CUIT' => $row[2],
                    'EMPRESA' => $row[3],
                    'PERIODO' => $row[4],
                    'CUIL' => $row[5],
                    'NOMBRE' => $row[6],
                    'REMUNERA' => $row[7],
                    'APORTE' => $row[8],
                    'CONTRI' => $row[9],
                    'MONO' => $row[10],
                    'OTROS' => $row[11],
                    'TOTAL' => $row[12],
                    'OBRA_SOCIAL' => 'OSFOT',
                ]);
            }
        } else if ($this->tipo_archivo == 2) {
            foreach ($rows as $row) {
                LiquidacionOsmitaModel::create([
                    'tipoaf' => $row[0],
                    'cuit' => $row[1],
                    'razonsoc' => $row[2],
                    'cuil' => $row[3],
                    'nomyape' => $row[4],
                    'nroaf' => $row[5],
                    'codaf' => $row[6],
                    'sistmed' => $row[7],
                    'nroasoc' => $row[8],
                    'capitas' => $row[9],
                    'fec_alta' => \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row[10]),
                    'periodo' => $row[11],
                    'rence' => $row[12],
                    'remap' => $row[13],
                    'djtotce' => $row[14],
                    'djtotap' => $row[15],
                    'apoce' => $row[16],
                    'apoap' => $row[17],
                    'apoyco' => $row[18],
                    'a_pagar' => $row[19],
                    'fec_rec' => \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row[20]),
                    'codconc' => $row[21],
                    'OBRA_SOCIAL' => 'OSMITA',

                ]);
            }
        } else if ($this->tipo_archivo == 1) {
            foreach ($rows as $row) {
                LiquidacionOsycModel::create([
                    'id' => $row[0],
                    'organ' => $row[1],
                    'codconc' => $row[2],
                    'importe' => str_replace(',', '.', trim($row[3])),
                    'fecproc' => \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row[4]),
                    'fecrec' => \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row[5]),
                    'cuitcont' => $row[6],
                    'periodo' => $row[7],
                    'idtranfer' => $row[8],
                    'cuitapo' => $row[9],
                    'banco' => $row[10],
                    'codsuc' => $row[11],
                    'zona' => $row[12],
                    'gerenciador' => $row[13],
                    'afiliado_nombre' => $row[14],
                    'afiliado_apellido' => $row[15],
                    'activo' => ($row[16] === 'True' || $row[16] === true) ? 1 : 0,
                    'razonsocial' => $row[17],
                    'obra_social' => 'OSYC',
                ]);
            }
        } else if ($this->tipo_archivo == 4) {
            foreach ($rows as $row) {
                LiquidacionPrensa::create([
                    'id' => $row[0],
                    'organ' => $row[1],
                    'codconc' => $row[2],
                    'importe' => str_replace(',', '.', trim($row[3])),
                    'fecproc' => \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row[4]),
                    'fecrec' => \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row[5]),
                    'cuitcont' => $row[6],
                    'periodo' => $row[7],
                    'idtranfer' => $row[8],
                    'cuitapo' => $row[9],
                    'banco' => $row[10],
                    'codsuc' => $row[11],
                    'zona' => $row[12],
                    'gerenciador' => $row[13],
                    'afiliado_nombre' => $row[14],
                    'afiliado_apellido' => $row[15],
                    'activo' => ($row[16] === 'True' || $row[16] === true) ? 1 : 0,
                    'razonsocial' => $row[17],
                    'obra_social' => 'PRENSA',
                ]);
            }
        } else if ($this->tipo_archivo == 5) {
            foreach ($rows as $row) {
                LiquidacionOsceara::create([
                    'apellido_nombre' => $row[0],
                    'nombre' => $row[1],
                    'cuil' => $row[2],
                    'cuit' => $row[3],
                    'nro_afiliado' => $row[4],
                    'periodo' => $row[5],
                    'empresa' => $row[6],
                    'remun_rem' => $row[7],
                    'remdj_ct' => $row[8],
                    'remdj_st' => $row[9],
                    'apo_trf' => $row[10],
                    'con_trf' => $row[11],
                    'tot_trf' => $row[12],
                    'impdj' => $row[13],
                    'obra_social' => 'OSCEARA',
                ]);
            }
        } else if ($this->tipo_archivo == 6) {
            foreach ($rows as $row) {
                LiquidacionOsetya::create([
                    'id' => $row[0],
                    'organ' => $row[1],
                    'codconc' => $row[2],
                    'importe' => str_replace(',', '.', trim($row[3])),
                    'fecproc' => \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row[4]),
                    'fecrec' => \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row[5]),
                    'cuitcont' => $row[6],
                    'periodo' => $row[7],
                    'idtranfer' => $row[8],
                    'cuitapo' => $row[9],
                    'banco' => $row[10],
                    'codsuc' => $row[11],
                    'zona' => $row[12],
                    'gerenciador' => $row[13],
                    'afiliado_nombre' => $row[14],
                    'afiliado_apellido' => $row[15],
                    'activo' => ($row[16] === 'True' || $row[16] === true) ? 1 : 0,
                    'razonsocial' => $row[17],
                    'obra_social' => 'OSETYA',
                ]);
            }
        } else if ($this->tipo_archivo == 7) {
            foreach ($rows as $row) {
                LiquidacionOstvModel::create([
                    'id' => $row[0],
                    'organ' => $row[1],
                    'codconc' => $row[2],
                    'importe' => str_replace(',', '.', trim($row[3])),
                    'fecproc' => \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row[4]),
                    'fecrec' => \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row[5]),
                    'cuitcont' => $row[6],
                    'periodo' => $row[7],
                    'idtranfer' => $row[8],
                    'cuitapo' => $row[9],
                    'banco' => $row[10],
                    'codsuc' => $row[11],
                    'zona' => $row[12],
                    'gerenciador' => $row[13],
                    'afiliado_nombre' => $row[14],
                    'afiliado_apellido' => $row[15],
                    'activo' => ($row[16] === 'True' || $row[16] === true) ? 1 : 0,
                    'razonsocial' => $row[17],
                    'obra_social' => 'OSTV',
                ]);
            }
        }else{
            foreach ($rows as $row) {
                LiquidacionObrasSociales::create([
                    'nros' => $row[0],
                    'cuil' => $row[1],
                    'cuit' => $row[2],
                    'periodo_recibido' => $row[3],
                    'periodo_devengado' => $row[4],
                    'codigo_concepto' => $row[5],
                    'nombre_afiliado' => $row[6],
                    'trf_total' => $row[7],
                    'fecha_proceso' => $fechaActual,
                ]);
            }
        }
        // DB::commit();
        //} catch (\Throwable $exception) {
        //DB::rollBack();
        //  return response()->json(['message' => $exception->getMessage()], 500);
        //}

        $this->mensaje = 'Datos de archivo registrado correctamente';
    }

    public function startRow(): int
    {
        return 2;
    }
}
