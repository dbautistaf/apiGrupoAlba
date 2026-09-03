<?php

namespace App\Exports;

use App\Http\Controllers\Tesoreria\Repository\TesInstrumentoPagoRepository;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;

/**
 * Listado exportable de OPs con eCheq pendientes de número, agrupado por banco emisor.
 *
 * Es lo que Pagos manda a Tesorería para que emita: sale ordenado por banco y por número de OP,
 * con la columna de eCheq vacía para completar a mano con lo que devuelva el banco.
 */
class EcheqPendientesNumeroExport implements FromCollection, WithHeadings, ShouldAutoSize, WithStyles
{
    protected $repository;
    protected $idBanco;

    public function __construct(TesInstrumentoPagoRepository $repository, $idBanco = null)
    {
        $this->repository = $repository;
        $this->idBanco    = $idBanco;
    }

    public function collection()
    {
        return $this->repository->listarPendientesDeNumero($this->idBanco)
            ->map(function ($p) {
                return [
                    'banco'        => $p->bancoEmisor->descripcion_banco ?? 'SIN BANCO ASIGNADO',
                    'num_opa'      => $p->num_orden_pago,
                    'beneficiario' => TesInstrumentoPagoRepository::nombreBeneficiario($p->opa),
                    'numero_echeq' => $p->numero_echeq ?? '',
                    'monto'        => (float) $p->monto_pago,
                    'fecha_pago'   => $p->fecha_probable_pago,
                ];
            });
    }

    public function headings(): array
    {
        return [
            'BANCO EMISOR',
            'N° ORDEN DE PAGO',
            'PROVEEDOR / PRESTADOR',
            'N° ECHEQ',
            'MONTO',
            'FECHA DE PAGO',
        ];
    }

    public function styles($excel)
    {
        return [
            'A1:F1' => ['font' => ['bold' => true]],
        ];
    }
}
