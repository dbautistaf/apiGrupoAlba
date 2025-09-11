<?php

namespace App\Exports;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;

class RentabilidadExport implements FromCollection, WithHeadings, ShouldAutoSize, WithStyles, WithMapping
{
    /**
     * @return \Illuminate\Support\Collection
     */
    protected $params;

    public function __construct($param)
    {
        $this->params = $param;
    }
    public function collection()
    {
        $anio = $this->params->anio;
        $unidadNegocio = $this->params->unidad;
        $cuil = $this->params->cuil;
        $cuit = $this->params->cuit;

        $meses = ['12', '11', '10', '09', '08', '07', '06', '05', '04', '03', '02', '01'];
        $sumSelects = [];

        foreach ($meses as $mes) {
            $periodo = $anio . $mes;
            $sumSelects[] = DB::raw("SUM(CASE WHEN b.periodo = '$periodo' THEN b.importe ELSE 0 END) AS aporte_$mes");
            $sumSelects[] = DB::raw("SUM(CASE WHEN b.periodo = '$periodo' THEN b.importe * 2 ELSE 0 END) AS contrib_$mes");
        }

        if ($cuil != '' || $cuit != '') {
            $query = DB::table('tb_padron as a')
                ->leftJoin('tb_transferencias as b', function ($join) use ($anio) {
                    $join->on('a.cuil_tit', '=', 'b.cuitapo')
                        ->whereRaw("LEFT(b.periodo, 2) = ?", [$anio]);
                })
                ->where('a.apellidos', '<>', ' ');
            if (!empty($cuil)) {
                $query->where('a.cuil_tit', $cuil);
            } elseif (!empty($cuit)) {
                $query->where('b.cuitcont', $cuit);
            }
            $query->select(array_merge([
                'a.cuil_tit',
                'a.dni',
                'a.nombre',
                'a.apellidos',
                'a.fe_alta',
                'a.fe_baja',
                'a.observaciones'
            ], $sumSelects));
            $query->groupBy('a.cuil_tit', 'a.dni', 'a.nombre', 'a.apellidos', 'a.fe_alta', 'a.fe_baja', 'a.observaciones');

            return $query->get();
        } else {
            $query = DB::table('tb_padron as a')
                ->leftJoin('tb_transferencias as b', function ($join) use ($anio) {
                    $join->on('a.cuil_tit', '=', 'b.cuitapo')
                        ->whereRaw("LEFT(b.periodo, 2) = ?", [$anio]);
                })
                ->where('a.id_unidad_negocio', $unidadNegocio)
                ->where('a.apellidos', '<>', ' ')
                ->select(array_merge([
                    'a.cuil_tit',
                    'a.dni',
                    'a.nombre',
                    'a.apellidos',
                    'a.fe_alta',
                    'a.fe_baja',
                    'a.observaciones'
                ], $sumSelects))
                ->groupBy('a.cuil_tit', 'a.dni', 'a.nombre', 'a.apellidos', 'a.fe_alta', 'a.fe_baja', 'a.observaciones')
                ->orderBy('a.cuil_tit')
                ->orderBy('a.dni')
                ->get();

            return $query;
        }
    }

    public function headings(): array
    {
        return [
            'DNI',
            'CUIL',
            'NOMBRES',
            'APELLIDOS',
            'FECHA ALTA',
            'OBSERVACIONES',
            'APORTE_' . $this->params->anio . '_01',
            'CONTRIB_' . $this->params->anio . '_01',
            'APORTE_' . $this->params->anio . '_02',
            'CONTRIB_' . $this->params->anio . '_02',
            'APORTE_' . $this->params->anio . '_03',
            'CONTRIB_' . $this->params->anio . '_03',
            'APORTE_' . $this->params->anio . '_04',
            'CONTRIB_' . $this->params->anio . '_04',
            'APORTE_' . $this->params->anio . '_05',
            'CONTRIB_' . $this->params->anio . '_05',
            'APORTE_' . $this->params->anio . '_06',
            'CONTRIB_' . $this->params->anio . '_06',
            'APORTE_' . $this->params->anio . '_07',
            'CONTRIB_' . $this->params->anio . '_07',
            'APORTE_' . $this->params->anio . '_08',
            'CONTRIB_' . $this->params->anio . '_08',
            'APORTE_' . $this->params->anio . '_09',
            'CONTRIB_' . $this->params->anio . '_09',
            'APORTE_' . $this->params->anio . '_10',
            'CONTRIB_' . $this->params->anio . '_10',
            'APORTE_' . $this->params->anio . '_11',
            'CONTRIB_' . $this->params->anio . '_11',
            'APORTE_' . $this->params->anio . '_12',
            'CONTRIB_' . $this->params->anio . '_12',
        ];
    }

    public function styles($excel)
    {
        return [
            'A1:BB1' => ['font' => ['bold' => true]],
        ];
    }

    public function map($row): array
    {
        return [
            $row->dni,
            $row->cuil_tit,
            $row->nombre,
            $row->apellidos,
            $row->fe_alta,
            $row->observaciones,
            $row->aporte_01,
            $row->contrib_01,
            $row->aporte_02,
            $row->contrib_02,
            $row->aporte_03,
            $row->contrib_03,
            $row->aporte_04,
            $row->contrib_04,
            $row->aporte_05,
            $row->contrib_05,
            $row->aporte_06,
            $row->contrib_06,
            $row->aporte_07,
            $row->contrib_07,
            $row->aporte_08,
            $row->contrib_08,
            $row->aporte_09,
            $row->contrib_09,
            $row->aporte_10,
            $row->contrib_10,
            $row->aporte_11,
            $row->contrib_11,
            $row->aporte_12,
            $row->contrib_12,
        ];
    }
}
