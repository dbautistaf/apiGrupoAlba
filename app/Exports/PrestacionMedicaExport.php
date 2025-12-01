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
                "prestadorefector",
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
                    'fecha_registra'       => $p->fecha_registra ?? 'N/A',
                    'fecha_registra'       => $p->fecha_registra ?? 'N/A',
                    'solicitante'          => $p->prestador?->razon_social ?? 'N/A',
                    'efector'              => $p->prestadorefector?->razon_social ?? 'N/A',
                    'imputacion_total'     => 'N/A',
                    'imputacion_detalle'   => 'N/A',
                    'origen'               => $p->datosTramite?->obrasocial?->locatorio ?? 'N/A',
                    'num_filiado'          => $p->afiliado?->cuil_benef ?? 'N/A',
                    'nombre_completo'      => trim(($p->afiliado?->nombre ?? '') . ' ' . ($p->afiliado?->apellidos ?? '')),
                    'edad'                 => $p->afiliado?->fe_nac
                        ? \Carbon\Carbon::parse($p->afiliado->fe_nac)->age
                        : 'N/A',
                    'sexo'                 => $p->afiliado?->sexo?->sexo ?? 'N/A',
                    'dni'                  => $p->afiliado?->dni ?? 'N/A',
                    'practica'             => $d->practica?->nombre_practica ?? 'N/A',
                    'importe_facturado'    => number_format($d->monto_pagar ?? 0, 2, '.', ''),
                    'importe_aprobado'     => number_format($d->monto_pagar ?? 0, 2, '.', ''),
                ]);
            }
        }

        return $rows;
    }

    public function headings(): array
    {
        return [
            'Fecha Ingreso',
            'Fecha Emision',
            'Solicitante',
            'Efector',
            'Imputacion Contable (Total)',
            'Imputacion Contable (Detalle)',
            'Origen',
            'Nro.Afiliado',
            'Nombre y Apellido Afiliado',
            'Edad',
            'Sexo',
            'Nº DNI',
            'Practica/Medicamento Descripción',
            'Importe Facturado',
            'Importe Aprobado',

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
            $row['fecha_registra'],
            $row['fecha_registra'],
            $row['solicitante'],
            $row['efector'],
            $row['imputacion_total'],
            $row['imputacion_detalle'],
            $row['origen'],
            $row['num_filiado'],
            $row['nombre_completo'],
            $row['edad'],
            $row['sexo'],
            $row['dni'],
            $row['practica'],
            $row['importe_facturado'],
            $row['importe_aprobado'],
        ];
    }
}
