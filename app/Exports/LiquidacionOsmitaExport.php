<?php

namespace App\Exports;

use App\Models\liquidacion\LiquidacionOsmitaModel;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;

class LiquidacionOsmitaExport implements FromCollection, WithHeadings, ShouldAutoSize, WithStyles, WithMapping
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

        return LiquidacionOsmitaModel::query()
            ->select([
                'tipoaf',
                'cuit',
                'razonsoc',
                'cuil',
                'nomyape',
                'nroaf',
                'codaf',
                'sistmed',
                'nroasoc',
                'capitas',
                'fec_alta',
                'periodo',
                'rence',
                'remap',
                'djtotce',
                'djtotap',
                'apoce',
                'apoap',
                'apoyco',
                'a_pagar',
                'fec_rec',
                'codconc',
                'OBRA_SOCIAL',
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
            'tipoaf',
            'cuit',
            'razonsoc',
            'cuil',
            'nomyape',
            'nroaf',
            'codaf',
            'sistmed',
            'nroasoc',
            'capitas',
            'fec_alta',
            'periodo',
            'rence',
            'remap',
            'djtotce',
            'djtotap',
            'apoce',
            'apoap',
            'apoyco',
            'a_pagar',
            'fec_rec',
            'codconc',
            'OBRA_SOCIAL',
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
            $row->tipoaf,
            $row->cuit,
            $row->razonsoc,
            $row->cuil,
            $row->nomyape,
            $row->nroaf,
            $row->codaf,
            $row->sistmed,
            $row->nroasoc,
            $row->fec_alta,
            $row->periodo,
            $row->rence,
            $row->remap,
            $row->djtotce,
            $row->djtotap,
            $row->apoce,
            $row->apoap,
            $row->apoyco,
            $row->a_pagar,
            $row->fec_rec,
            $row->codconc,
            $row->OBRA_SOCIAL,
            optional($row->PadronAfil)->id_unidad_negocio
        ];
    }
}
