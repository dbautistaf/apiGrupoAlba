<?php

namespace App\Exports;

use App\Models\TransferenciasModelo;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;

class Transferencias implements FromCollection, WithHeadings, ShouldAutoSize, WithStyles, WithMapping
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
        
        return TransferenciasModelo::query()
        ->select([
            'cuitcont',
            'cuitapo',
            'fecrec',
            'fecproc',
            'codconc',
            'inddbcr',            
            'importe',
        ])
            ->with([
                'PadronAfil','Empresa'
            ])
            ->when($this->params->periodo != 'NINGUNA', function ($query) {
                $query->where('periodo_tranf', $this->params->periodo);
            })
            ->when(!empty($this->params->desde) && !empty($this->params->hasta), function ($query) {
                $query->whereBetween('fecha_proceso', [$this->params->desde, $this->params->hasta]);
            })
            ->when(!empty($this->params->cuit), function ($query) {
                $query->where('cuitcont', 'LIKE', "{$this->params->cuit}%");
            })
            ->when(!empty($this->params->cuil), function ($query) {
                $query->where('cuitapo', 'LIKE', "{$this->params->cuil}%");
            })
            ->when(!empty($this->params->locatario), function ($query) {
                $query->whereHas('PadronAfil', function ($q) {
                    $q->where('id_locatario', 'LIKE', "{$this->params->locatario}%");
                });
            })
            ->when(!empty($this->params->unidad), function ($query) {
                $query->whereHas('PadronAfil', function ($q) {
                    $q->where('id_unidad_negocio', 'LIKE', "%{$this->params->unidad}%");
                });
            })->limit(69000)->get();
    }

    public function headings(): array
    {
        return [
           'Cuit Empresa',
            'Razón Social',
            'CUIL BENEF',            
            'Nombre Completo',
            'Fecha Presentación',
            'Fecha Proceso',
            'Concepto',
            'INDDBCR',
            'Aporte',
            'Contribucion',
            'Total',
            'Provincia',
            'fecha alta',
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
            $row->cuitcont,
            $row->Empresa->razon_social ?? 'N/A', 
            $row->cuitapo,
            optional($row->PadronAfil)->nombre . ' ' . optional($row->PadronAfil)->apellidos,    
            $row->fecpresent,
            $row->fecproc,
            $row->codconc,
            $row->inddbcr,
            $row->importe,
            number_format($row->importe * 2, 2, '.', ''),
            number_format($row->importe * 3, 2, '.', ''),
            $row->PadronAfil->id_provincia ?? 'N/A', 
            $row->PadronAfil->fe_alta ?? 'N/A', 
        ];
    }
}
