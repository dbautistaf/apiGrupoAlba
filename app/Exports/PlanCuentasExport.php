<?php

namespace App\Exports;

use App\Models\Contabilidad\DetallePlanCuentasEntity;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;

class PlanCuentasExport implements FromCollection, WithHeadings, ShouldAutoSize, WithStyles
{
    public function collection()
    {
        return DetallePlanCuentasEntity::query()
            ->select(['codigo_cuenta', 'cuenta'])
            ->orderBy('codigo_cuenta', 'asc')
            ->get();
    }

    public function headings(): array
    {
        return [
            'COD_CUENTA',
            'CUENTA'
        ];
    }

    public function styles($excel)
    {
        return [
            'A1:B1' => ['font' => ['bold' => true]],
        ];
    }
}