<?php

namespace App\Exports;

use App\Models\DeclaracionesJuradasModelo;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;

class DeclaracionesJuradas implements FromCollection, WithHeadings, ShouldAutoSize, WithStyles, WithMapping
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
        
        return DeclaracionesJuradasModelo::query()
        ->select([
            'cuit',
            'cuil',
            'remimpo',
            'fecpresent',
            'fecproc',
            'imposad',
            'secoblig',
            'situacion',
            'actividad',
            'modalidad',
        ])
            ->with([
                'PadronAfil','Empresa'
            ])
            ->when($this->params->periodo != 'NINGUNA', function ($query) {
                $query->where('periodo_ddjj', $this->params->periodo);
            })
            ->when(!empty($this->params->desde) && !empty($this->params->hasta), function ($query) {
                $query->whereBetween('fecha_proceso', [$this->params->desde, $this->params->hasta]);
            })
            ->when(!empty($this->params->cuit), function ($query) {
                $query->where('cuit', 'LIKE', "{$this->params->cuit}%");
            })
            ->when(!empty($this->params->cuil), function ($query) {
                $query->where('cuil', 'LIKE', "{$this->params->cuil}%");
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
            })->limit(60000)->get();
    }

    public function headings(): array
    {
        return [
           'Cuit Empresa',
            'Razón Social',
            'CUIL BENEF',            
            'Nombre Completo',
            'Remimpo',
            'Fecha Presentación',
            'Fecha Proceso',
            'Imposad',
            'Sec Oblig',
            'Situación',
            'Actividad',
            'Modalidad',
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
            $row->cuit,
            $row->Empresa->razon_social ?? 'N/A', 
            $row->cuil,
            optional($row->PadronAfil)->nombre . ', ' . optional($row->PadronAfil)->apellidos,    
            $row->remimpo,
            $row->fecpresent,
            $row->fecproc,
            $row->imposad,
            $row->secoblig,
            $row->situacion,
            $row->actividad,
            $row->modalidad,
        ];
    }
}
