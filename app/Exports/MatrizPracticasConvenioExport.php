<?php

namespace App\Exports;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;

class MatrizPracticasConvenioExport implements FromCollection, WithHeadings, ShouldAutoSize, WithStyles
{
    use Exportable;

    protected $grupoFechas = null, $codConvenio = null;

    public function __construct(string $grupoFechas, string $codConvenio)
    {
        $this->grupoFechas = $grupoFechas;
        $this->codConvenio = $codConvenio;
    }

    public function collection()
    {
        $query = null;
        if(!is_null($this->grupoFechas)){
            $query = DB::table('vw_matriz_historial_costos')
            ->select('codigo_practica', 'nombre_practica', 'monto_gastos', 'fecha_inicio')
            ->where('cod_convenio', $this->codConvenio)
            ->whereDate('fecha_inicio', $this->grupoFechas)
            ->get();
        }else{
            $query = DB::table('vw_matriz_historial_costos')
            ->select('codigo_practica', 'nombre_practica', 'monto_gastos', 'fecha_inicio')
            ->where('cod_convenio', $this->codConvenio)
            ->where('vigente', '1')
            ->get();
        }

        return $query;
    }

    public function headings(): array
    {
        return [
            'CODIGO PRACTICA',
            'NOMBRE PRACTICA',
            'VALOR GASTO',
            'VIGENCIA DESDE'
        ];
    }

    public function styles($excel)
    {
        return [
            'A1:BB1' => ['font' => ['bold' => true]],
        ];
    }
}
