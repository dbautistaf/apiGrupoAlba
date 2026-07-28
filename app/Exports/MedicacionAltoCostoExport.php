<?php

namespace App\Exports;

use App\Models\medicacionAltoCosto\MedicacionAltoCosto;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Carbon\Carbon;

class MedicacionAltoCostoExport implements FromCollection, WithHeadings, ShouldAutoSize, WithStyles, WithMapping
{
    protected $mes;
    protected $anio;

    public function __construct($mes, $anio)
    {
        $this->mes = $mes;
        $this->anio = $anio;
    }

    public function collection()
    {
        $startDate = Carbon::create($this->anio, $this->mes, 1)->startOfMonth()->format('Y-m-d');
        $endDate = Carbon::create($this->anio, $this->mes, 1)->endOfMonth()->format('Y-m-d');

        return MedicacionAltoCosto::query()
            ->with(['afiliado', 'autorizacion', 'presupuesto.prestador'])
            ->where('estado_registro', 'ACTIVO')
            ->whereBetween('fecha_registro', [$startDate, $endDate])
            ->get();
    }

    public function headings(): array
    {
        return [
            'N° de tramite',
            'Documento Afiliado',
            'Apellidos y Nombres',
            'Medico',
            'Fecha de registro',
            'Estado pago',
            'Ganador',
        ];
    }

    public function styles($excel)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }

    public function map($row): array
    {
        $nombreCompleto = $row->afiliado ? $row->afiliado->apellidos . ' ' . $row->afiliado->nombre : 'N/A';
        $medico = $row->matricula_medico . ' - ' . $row->nombre_medico;
        $estado = $row->autorizacion ? $row->autorizacion->detalle_autorizacion : 'N/A';
        $ganador = $row->prestador_ganador ? $row->prestador_ganador->nombre_fantasia : 'No definido';

        return [
            $row->numero_tramite,
            $row->dni_afiliado,
            $nombreCompleto,
            $medico,
            $row->fecha_registro ? Carbon::parse($row->fecha_registro)->format('d/m/Y') : '',
            $estado,
            $ganador,
        ];
    }
}
