<?php

namespace App\Exports;

use App\Models\liquidacion\LiquidacionOsfotModel;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;

class LiquidacionOsfotExport implements FromCollection, WithHeadings, ShouldAutoSize, WithStyles, WithMapping
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
        //

        return LiquidacionOsfotModel::query()
            ->select([
                'CONVENIO',
                'FILIAL',
                'CUIT',
                'EMPRESA',
                'PERIODO',
                'CUIL',
                'NOMBRE',
                'REMUNERA',
                'APORTE',
                'CONTRI',
                'MONO',
                'OTROS',
                'TOTAL',
                'OBRA_SOCIAL'
            ])
            ->with([
                'PadronAfil'
            ])
            ->when(!empty($this->params->cuit), function ($query) {
                $query->where('cuit', 'LIKE', "{$this->params->cuit}%");
            })
            ->when(!empty($this->params->cuil), function ($query) {
                $query->where('cuil', 'LIKE', "{$this->params->cuil}%");
            })
            ->when(!empty($this->params->unidad), function ($query) {
                $query->whereHas('PadronAfil', function ($q) {
                    $q->where('id_unidad_negocio', '=', $this->params->unidad);
                });
            })->limit(60000)->get();
    }

    public function headings(): array
    {
        return [
            'CONVENIO',
            'FILIAL',
            'CUIT',
            'EMPRESA',
            'PERIODO',
            'CUIL',
            'NOMBRE',
            'REMUNERA',
            'APORTE',
            'CONTRI',
            'MONO',
            'OTROS',
            'TOTAL',
            'OBRA_SOCIAL',
            'UNIDAD NEGOCIO'
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
            $row->CONVENIO,
            $row->FILIAL,
            $row->CUIT,
            $row->EMPRESA,
            $row->PERIODO,
            $row->CUIL,
            $row->NOMBRE,
            $row->REMUNERA,
            $row->APORTE,
            $row->CONTRI,
            $row->MONO,
            $row->OTROS,
            $row->TOTAL,
            $row->OBRA_SOCIAL,
            optional($row->PadronAfil)->id_unidad_negocio
        ];
    }
}
