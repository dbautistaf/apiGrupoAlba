<?php

namespace App\Exports\Internacion;

use App\Models\Internaciones\InternacionesEntity;
use App\Models\PrestacionesMedicas\DetallePrestacionesPracticaLaboratorioEntity;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Illuminate\Support\Collection;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class InternacionExport implements FromCollection, WithHeadings, ShouldAutoSize, WithStyles, WithMapping
{
    protected $data;
    protected $filtros;
    
    public function __construct($data, $filtros = null)
    {
        $this->data = $data;
        $this->filtros = $filtros;
    }

    public function collection()
    {
        $rows = new Collection();

        foreach ($this->data as $internacion) {
            $detalles = DetallePrestacionesPracticaLaboratorioEntity::with(["practica", "prestacion"])
                ->whereHas('prestacion', function ($query) use ($internacion) {
                    $query->where('cod_internacion', $internacion->cod_internacion);
                })
                ->get();

            // SOLUCIÓN: Si hay detalles, mostrar SOLO EL PRIMERO o resumen
            if ($detalles->isEmpty()) {
                // Sin detalles - una fila
                $rows->push([
                    'internacion' => $internacion,
                    'detalle' => null,
                    'total_detalles' => 0
                ]);
            } else {
                // CON detalles - UNA SOLA FILA con el primer detalle o resumen
                $rows->push([
                    'internacion' => $internacion,
                    'detalle' => $detalles->first(), // Solo el primer detalle
                    'total_detalles' => $detalles->count(),
                    'todos_detalles' => $detalles // Guardar todos por si necesitas
                ]);
            }
        }

        \Log::info('Total filas en export: ' . $rows->count());
        return $rows;
    }

    public function headings(): array
    {
        return [
            'N° Internación',
            'Apellidos y Nombres',
            'Marca (Obra Social)',
            'Institución',
            'Tipo Internación',
            'Fecha Internación',
            'Fecha Egreso',
            'Cantidad Días',
            'Total Prácticas',
            'Código Práctica',
            'Nombre Práctica',
            'Cant. Solicitada',
            'Cant. Autorizada',
            'Monto a Pagar',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }

    public function map($row): array
    {
        $internacion = $row['internacion'];
        $detalle = $row['detalle'];
        $totalDetalles = $row['total_detalles'];

        return [
            $internacion->num_internacion ?? '',
            trim(($internacion->afiliado?->apellidos ?? '') . ' ' . ($internacion->afiliado?->nombres ?? '')),
            $internacion->afiliado?->obrasocial?->locatorio ?? '',
            $internacion->prestador?->nombre_fantasia ?? '',
            $internacion->tipoInternacion?->descripcion ?? '',
            $internacion->fecha_internacion ?? '',
            $internacion->fecha_egreso ?? '',
            $internacion->cantidad_dias ?? '',
            $totalDetalles, // Total de prácticas
            $detalle?->practica?->codigo_practica ?? 'MÚLTIPLES',
            $detalle?->practica?->nombre_practica ?? ($totalDetalles > 0 ? 'Varias prácticas' : 'Sin prácticas'),
            $detalle?->cantidad_solicitada ?? '',
            $detalle?->cantidad_autorizada ?? '',
            $detalle?->monto_pagar ?? '',
        ];
    }
}