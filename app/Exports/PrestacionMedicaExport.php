<?php

namespace App\Exports;

use App\Models\PrestacionesMedicas\PrestacionesPracticaLaboratorioEntity;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;

class PrestacionMedicaExport implements FromCollection, WithHeadings, ShouldAutoSize, WithStyles, WithMapping
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
        $prestacion = PrestacionesPracticaLaboratorioEntity::with(
            [
                "detalle",
                "detalle.practica",
                "estadoPrestacion",
                "afiliado",
                "afiliado.obrasocial",
                "usuario",
                "prestador",
                "profesional",
                "datosTramite",
                "datosTramite.tramite",
                "datosTramite.prioridad",
                "datosTramite.obrasocial",
                "documentacion"
            ]
        )
            ->whereBetween('fecha_registra', [$this->params->desde, $this->params->hasta])
            //->orderByRaw("COALESCE(NULLIF(fecha_modifica, ''), fecha_registra) DESC")
            ->orderByDesc('cod_prestacion')
            ->get();
        $rows = collect();
        foreach ($prestacion as $p) {
            foreach ($p->detalle as $d) {
                $rows->push([
                    'tramite'              => $p->datosTramite?->tramite?->descripcion ?? 'N/A',
                    'numero_tramite'       => $p->numero_tramite ?? 'N/A',
                    'prioridad'            => $p->datosTramite?->prioridad?->descripcion ?? 'N/A',
                    'fecha_registra'       => $p->fecha_registra ?? 'N/A',
                    'cuil_benef'           => $p->afiliado?->cuil_benef ?? 'N/A',
                    'nombre_completo'      => trim(($p->afiliado?->nombre ?? '') . ' ' . ($p->afiliado?->apellidos ?? '')),
                    'monto_pagar_total'    => number_format($p->monto_pagar ?? 0, 2, '.', ''),
                    'codigo_practica'      => $d->practica?->codigo_practica ?? 'N/A',
                    'nombre_practica'      => $d->practica?->nombre_practica ?? 'N/A',
                    'razon_social'         => $p->prestador?->razon_social ?? 'N/A',
                    'profesional'          => $p->profesional?->apellidos_nombres ?? 'N/A',
                    'cantidad_solicitada'  => $d->cantidad_solicitada ?? 0,
                    'cantidad_autorizada'  => $d->cantidad_autorizada ?? 0,
                    'monto_detalle'        => number_format($d->monto_pagar ?? 0, 2, '.', ''),
                    'diagnostico'          => $p->diagnostico,
                    'obrasocial'           => $p->datosTramite?->obrasocial?->locatorio,
                    'usuario'              => $p->usuario?->nombre_apellidos
                ]);
            }
        }

        return $rows;
    }

    public function headings(): array
    {
        return [
            'Tipo Tramite',
            'Nº Tramite',
            'Prioridad',
            'Fecha Prestación',
            'CUIL',
            'Afiliado',
            'monto_pagar',
            'codigoPractica',
            'nombre_practica',
            'razon_social',
            'Profesional',
            'cantidad_solicitada',
            'cantidad_autorizada',
            'montoPagar',
            'diagnostico',
            'obrasocial',
            'usuario'

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
            $row['tramite'],
            $row['numero_tramite'],
            $row['prioridad'],
            $row['fecha_registra'],
            $row['cuil_benef'],
            $row['nombre_completo'],
            $row['monto_pagar_total'],
            $row['codigo_practica'],
            $row['nombre_practica'],
            $row['razon_social'],
            $row['profesional'],
            $row['cantidad_solicitada'],
            $row['cantidad_autorizada'],
            $row['monto_detalle'],
            $row['diagnostico'],
            $row['obrasocial'],
            $row['usuario'],
        ];
    }
}
