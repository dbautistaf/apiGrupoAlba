<?php

namespace App\Exports;

use App\Models\liquidacion\LiquidacionOsceara;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;

class LiquidacionOscearaExport implements FromCollection, WithHeadings, ShouldAutoSize, WithStyles, WithMapping
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

        return LiquidacionOsceara::query()
            ->select([
                'apellido_nombre',
                'nombre',
                'cuil',
                'cuit',
                'nro_afiliado',
                'periodo',
                'empresa',
                'remun_rem',
                'remdj_ct',
                'remdj_st',
                'apo_trf',
                'con_trf',
                'tot_trf',
                'impdj',
                'obra_social',
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
            'apellido_nombre',
            'nombre',
            'cuil',
            'cuit',
            'nro_afiliado',
            'periodo',
            'empresa',
            'remun_rem',
            'remdj_ct',
            'remdj_st',
            'apo_trf',
            'con_trf',
            'tot_trf',
            'impdj',
            'obra_social',
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
            $row->apellido_nombre,
            $row->nombre,
            $row->cuil,
            $row->cuit,
            $row->nro_afiliado,
            $row->periodo,
            $row->empresa,
            $row->remun_rem,
            $row->remdj_ct,
            $row->remdj_st,
            $row->apo_trf,
            $row->con_trf,
            $row->tot_trf,
            $row->impdj,
            $row->obra_social,
            optional($row->PadronAfil)->id_unidad_negocio
        ];
    }
}
