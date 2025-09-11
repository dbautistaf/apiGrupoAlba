<?php

namespace App\Exports;

use App\Models\liquidacion\LiquidacionPrensa;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;

class LiquidacionPrensaExport implements FromCollection, WithHeadings, ShouldAutoSize, WithStyles, WithMapping
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

        return LiquidacionPrensa::query()
            ->select([
                'id',
                'organ',
                'codconc',
                'importe',
                'fecproc',
                'fecrec',
                'cuitcont',
                'periodo',
                'idtranfer',
                'cuitapo',
                'banco',
                'codsuc',
                'zona',
                'gerenciador',
                'afiliado_nombre',
                'afiliado_apellido',
                'activo',
                'razonsocial',
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
            'id',
            'organ',
            'codconc',
            'importe',
            'fecproc',
            'fecrec',
            'cuitcont',
            'periodo',
            'idtranfer',
            'cuitapo',
            'banco',
            'codsuc',
            'zona',
            'gerenciador',
            'afiliado nombre',
            'afiliado apellido',
            'activo',
            'razonsocial',
            'obra social'
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
            $row->id,
            $row->organ,
            $row->codconc,
            $row->importe,
            $row->fecproc,
            $row->fecrec,
            $row->cuitcont,
            $row->periodo,
            $row->idtranfer,
            $row->cuitapo,
            $row->banco,
            $row->zona,
            $row->gerenciador,
            $row->afiliado_nombre,
            $row->afiliado_apellido,
            $row->activo,
            $row->razonsocial,
            $row->obra_social,
            optional($row->PadronAfil)->id_unidad_negocio
        ];
    }
}
