<?php

namespace App\Exports;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;

class PracticasExport implements FromCollection, WithHeadings, ShouldAutoSize, WithStyles
{
    /**
     * @return \Illuminate\Support\Collection
     */
    use Exportable;
    public function __construct() {}

    public function collection()
    {
        //
        $query = DB::select("SELECT cobertura,cod_categoria_internacion,codigo_practica,coseguro,especialista,
                                fecha_vigencia,galeno_adicional,galeno_aparatologia,
                                galeno_gasto,id_identificador_practica,id_nomenclador,
                                id_padre,id_practica_valorizacion,id_seccion,id_tipo_galeno,nombre_practica, nomenclador,
                                seccion, vigente FROM vw_matriz_practicas  ORDER BY codigo_practica");
        return collect($query);
    }

    public function headings(): array
    {
        return [
            'cobertura',
            'cod categoria internacion',
            'codigo practica',
            'coseguro',
            'especialista',
            'fecha vigencia',
            'galeno adicional',
            'galeno aparatologia',
            'galeno gasto',
            'id identificador practica',
            'id nomenclador',
            'id padre',
            'id practica valorizacion',
            'id seccion',
            'id tipo galeno',
            'nombre practica',
            'nomenclador'
        ];
    }

    public function styles($excel)
    {
        return [
            'A1:BB1' => ['font' => ['bold' => true]],
        ];
    }
}
