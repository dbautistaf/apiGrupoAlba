<?php

namespace App\Exports;

use App\Models\PrestacionesMedicas\PrestacionesPracticaLaboratorioEntity;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;

class PrestacionMedicaDetalleExport implements FromCollection, WithHeadings, ShouldAutoSize, WithStyles, WithMapping
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
                    'tramite'       => $p->datosTramite?->tramite?->descripcion ?? 'N/A',
                    'numero'       => $p->numero_tramite ?? 'N/A',
                    'prioridad'          => $p->datosTramite?->prioridad?->descripcion ?? 'N/A',
                    'fech_prestacion'              => $p->fecha_registra ?? 'N/A',
                    'afiliado'     => $p->afiliado?->nombre .' '. $p->afiliado?->apellidos,
                    'origen'               => $p->datosTramite?->obrasocial?->locatorio ?? 'N/A',
                    'codigo'          => $d->practica?->codigo_practica ?? 'N/A',
                    'nombre_practica'      =>$d->practica?->nombre_practica,
                    'prestador'                 => $p->prestador?->razon_social ?? 'N/A',
                    'medico'                  => $p->profesional?->apellidos_nombres ?? 'N/A',
                    'cant_solicitadoa'             => $d->cantidad_solicitada ?? 'N/A',
                    'cant_autorizada'             => $d->cantidad_autorizada ?? 'N/A',
                    'monto'    => number_format($d->monto_pagar ?? 0, 2, '.', ''),
                    'importe'   => number_format($p->monto_pagar?? 0, 2, '.', ''),
                    'usuario'               => $p->usuario?->nombre_apellidos ?? 'N/A',
                ]);
            }
        }

        return $rows;
    }

    public function headings(): array
    {
        return [
            'Tipo Tramite',
            'N° Tramite',
            'Prioridad',
            'Fecha Prestacion',
            'Afiliado',
            'origen',
            'Cod. Practica',
            'Practica',
            'Prestador',
            'Medico Efector',
            'Cant. Solocitada',
            'Cant. Autorizada',
            'Costo',
            'Importe',
            'Usuario',

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
            $row['numero'],
            $row['prioridad'],
            $row['fech_prestacion'],
            $row['afiliado'],
            $row['origen'],
            $row['codigo'],
            $row['nombre_practica'],
            $row['prestador'],
            $row['medico'],
            $row['cant_solicitadoa'],
            $row['cant_autorizada'],
            $row['monto'],
            $row['importe'],
            $row['usuario'],
        ];
    }
}
