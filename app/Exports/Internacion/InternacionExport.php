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

class InternacionExport implements FromCollection, WithHeadings, ShouldAutoSize, WithStyles, WithMapping
{
    protected $params;
    
    public function __construct($param)
    {
        $this->params = $param;
    }

    public function collection()
    {
        // Traemos todas las internaciones con sus relaciones
        $internaciones = InternacionesEntity::with([
            "prestador",
            "tipoPrestacion",
            "tipoInternacion",
            "tipoHabitacion",
            "afiliado.obrasocial",
            "categoria",
            "especialidad",
            "tipoEgreso",
            "tipoDiagnostico",
            "usuario",
            "estadoPrestacion",
        ])
        ->whereBetween('fecha_internacion', [$this->params->desde, $this->params->hasta])
        ->orderBy('fecha_internacion', 'desc')
        ->get();

        $rows = new Collection();

        // Expandir cada internación por sus detalles
        foreach ($internaciones as $internacion) {
            $detalles = DetallePrestacionesPracticaLaboratorioEntity::with(["practica", "prestacion"])
                ->whereHas('prestacion', function ($query) use ($internacion) {
                    $query->where('cod_internacion', $internacion->cod_internacion);
                })
                ->get();

            if ($detalles->isEmpty()) {
                // Si no tiene detalles, igual agregamos una fila vacía
                $rows->push([
                    'internacion' => $internacion,
                    'detalle' => null,
                ]);
            } else {
                foreach ($detalles as $detalle) {
                    $rows->push([
                        'internacion' => $internacion,
                        'detalle' => $detalle,
                    ]);
                }
            }
        }

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
            'Código Práctica',
            'Nombre Práctica',
            'Cant. Solicitada',
            'Cant. Autorizada',
            'Monto a Pagar',
        ];
    }

    public function styles($sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }

    public function map($row): array
    {
        $internacion = $row['internacion'];
        $detalle = $row['detalle'];

        return [
            $internacion->num_internacion ?? '',
            trim(($internacion->afiliado?->apellidos ?? '') . ' ' . ($internacion->afiliado?->nombres ?? '')),
            $internacion->afiliado?->obrasocial?->locatorio ?? '',
            $internacion->prestador?->nombre_fantasia ?? '',
            $internacion->tipoInternacion?->descripcion ?? '',
            $internacion->fecha_internacion ?? '',
            $internacion->fecha_egreso ?? '',
            $internacion->cantidad_dias ?? '',
            $detalle?->practica?->codigo_practica ?? '',
            $detalle?->practica?->nombre_practica ?? '',
            $detalle?->cantidad_solicitada ?? '',
            $detalle?->cantidad_autorizada ?? '',
            $detalle?->monto_pagar ?? '',
        ];
    }
}
