<?php

namespace App\Exports\Prestadores;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Carbon\Carbon;

class PrestadoresFacturasImpagasExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithTitle, WithColumnWidths
{
    protected $data;
    protected $tipo;

    public function __construct($data, $tipo = 'detallado')
    {
        $this->data = $data;
        $this->tipo = $tipo;
    }

    public function collection()
    {
        if ($this->tipo === 'resumen') {
            return collect($this->data);
        }

        $collection = collect();

        foreach ($this->data as $prestador) {
            foreach ($prestador->facturas as $factura) {
                $collection->push((object) [
                    'prestador' => $prestador,
                    'factura' => $factura
                ]);
            }
        }

        return $collection;
    }

    public function headings(): array
    {
        if ($this->tipo === 'resumen') {
            return [
                'COD Prestador',
                'Razón Social',
                'CUIT',
                'Total Facturas Impagas',
                'Monto Total Impago'
            ];
        }

        return [
            'Delegación',
            'Tipo',
            'Fecha Comprobante',
            'Fecha Vencimiento',
            'CUIT Prestador',
            'Prestador Razón Social',
            'Período',
            'Comprobante',
            'Saldo',
            'Días Vencidos'
        ];
    }

    public function map($row): array
    {
        if ($this->tipo === 'resumen') {
            return [
                $row->prestador_id,
                $row->razon_social,
                $row->numero_documento,
                $row->total_facturas_impagas,
                number_format($row->monto_total_impago, 2)
            ];
        }

        $fechaVencimiento = $row->factura->fecha_vencimiento ?? null;
        $diasVencidos = 0;

        if ($fechaVencimiento) {
            $fechaVenc = Carbon::parse($fechaVencimiento);
            $hoy = now();
            if ($fechaVenc->lt($hoy)) {
                $diasVencidos = $fechaVenc->diffInDays($hoy);
            }
        }

        // Construir comprobante
        $comprobante = ($row->factura->tipo_letra ?? '') .
            ($row->factura->sucursal ? '-' . $row->factura->sucursal : '') .
            ($row->factura->numero ? '-' . $row->factura->numero : '');

        return [
            $row->factura->filial->nombre_sindicato ?? 'Sin delegación',
            $row->factura->tipoFactura->descripcion ?? 'Sin tipo',
            $row->factura->fecha_comprobante ? Carbon::parse($row->factura->fecha_comprobante)->format('d/m/Y') : 'Sin fecha',
            $fechaVencimiento ? Carbon::parse($fechaVencimiento)->format('d/m/Y') : 'Sin fecha',
            $row->prestador->cuit,
            $row->prestador->razon_social,
            $row->factura->periodo ?? '--',
            $comprobante,
            'S/ ' . number_format($row->factura->total_neto ?? 0, 2, ',', '.'),
            $diasVencidos
        ];
    }

    public function columnWidths(): array
    {
        if ($this->tipo === 'resumen') {
            return [
                'A' => 15, // COD Prestador
                'B' => 40, // Razón Social
                'C' => 15, // CUIT
                'D' => 20, // Total Facturas Impagas
                'E' => 18, // Monto Total Impago
            ];
        }

        return [
            'A' => 20, // Delegación
            'B' => 20, // Tipo
            'C' => 18, // Fecha Comprobante
            'D' => 18, // Fecha Vencimiento
            'E' => 15, // CUIT Prestador
            'F' => 40, // Prestador Razón Social
            'G' => 15, // Período
            'H' => 18, // Comprobante
            'I' => 15, // Saldo
            'J' => 15, // Días Vencidos
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $totalRows = $this->collection()->count() + 1;

        return [
            1 => ['font' => ['bold' => true, 'size' => 12]],
            'A1:J' . $totalRows => [
                'alignment' => [
                    'wrapText' => true,
                    'vertical' => 'center'
                ]
            ],
        ];
    }

    public function title(): string
    {
        return $this->tipo === 'resumen' ? 'Resumen Facturas Impagas' : 'Facturas Impagas Detallado';
    }
}
