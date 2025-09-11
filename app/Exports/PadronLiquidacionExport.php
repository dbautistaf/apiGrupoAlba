<?php

namespace App\Exports;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;

class PadronLiquidacionExport implements FromCollection, WithHeadings, ShouldAutoSize, WithStyles
{
    /**
     * @return \Illuminate\Support\Collection
     */
    use Exportable;
    public function collection()
    {
        //
        $query = DB::table('tb_padron')
            ->join('tb_localidad', 'tb_padron.id_localidad', '=', 'tb_localidad.id_localidad')
            ->join('tb_provincias', 'tb_padron.id_provincia', '=', 'tb_provincias.id_provincia')
            ->join('tb_detalle_padron_tipo_plan', 'tb_padron.id', '=', 'tb_detalle_padron_tipo_plan.id_padron')
            ->join('tb_tipo_plan', 'tb_detalle_padron_tipo_plan.id_tipo_plan', '=', 'tb_tipo_plan.id_tipo_plan')
            ->select(
                'tb_padron.cuil_tit',
                'tb_padron.dni',
                DB::raw('CONCAT(tb_padron.apellidos, " ", tb_padron.nombre) as nombre_padron'),
                'tb_tipo_plan.tipo',
                'tb_provincias.nombre',
                'tb_localidad.nombre as localidad',
            )->get();
        return $query;
    }

    public function headings(): array
    {
        return [
            'CUIL TITULAR',
            'DNI',
            'NOMBRES',            
            'TIPO PLAN',
            'PROVINCIA',
            'LOCALIDAD'
        ];
    }

    public function styles($excel)
    {
        return [
            'A1:BB1' => ['font' => ['bold' => true]],
        ];
    }
}
