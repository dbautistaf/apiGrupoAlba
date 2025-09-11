<?php

namespace App\Exports;

use App\Models\Tesoreria\TesOrdenPagoEntity;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;

class OrdenesPagoExport implements FromCollection, WithHeadings, ShouldAutoSize, WithStyles
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

        $query = TesOrdenPagoEntity::with(['estado', 'factura', 'factura.razonSocial', 'proveedor', 'prestador']);

        if (!is_null($this->params->tipo)) {
            $query->where('tipo_factura', $this->params->tipo);
        }

        if (!is_null($this->params->estado)) {
            $query->where('id_estado_orden_pago', $this->params->estado);
        }

        if (!is_null($this->params->monto_desde) && !is_null($this->params->monto_hasta)) {
            $query->whereBetween('monto_orden_pago', [$this->params->monto_desde, $this->params->monto_hasta]);
        }

        if (!is_null($this->params->desde) && !is_null($this->params->hasta)) {
            $query->whereBetween(DB::raw('DATE(fecha_genera)'), [$this->params->desde, $this->params->hasta]);
        }

        if (!is_null($this->params->beneficiario)) {
            $beneficiario = $this->params->beneficiario;
            $query->where(function ($q) use ($beneficiario) {
                $q->whereHas('proveedor', function ($subQuery) use ($beneficiario) {
                    $subQuery->where('razon_social', 'LIKE', "{$beneficiario}%");
                })->orWhereHas('prestador', function ($subQuery) use ($beneficiario) {
                    $subQuery->where('razon_social', 'LIKE', "{$beneficiario}%");
                });
            });
        }

        if (!is_null($this->params->id_locatorio)) {
            $locatorio = $this->params->id_locatorio;
            $query->where(function ($q) use ($locatorio) {
                $q->whereHas('factura', function ($subQuery) use ($locatorio) {
                    $subQuery->where('id_locatorio', $locatorio);
                });
            });
        }

        if ($this->params->pago_urgente == '1') {
            $query->where('pago_emergencia', $this->params->pago_urgente);
        }

        $opas = $query
            ->orderBy('id_estado_orden_pago')
            ->orderByDesc('fecha_probable_pago')
            ->get();

        $opas = $opas->map(function ($item) {
            return [
                'id_orden_pago' => $item->id_orden_pago,
                'numero_orden_pago' => $item->num_orden_pago,
                'fecha_probable_pago' => $item->fecha_probable_pago,
                'fecha_confirmar_pago' => $item->fecha_confirma_pago,
                'n_factura' => $item->factura ? $item->factura->numero : '',
                'n_liquidacion' => $item->factura ? $item->factura->num_liquidacion : '',
                'tipo_beneficiario' => $item->tipo_factura,
                'beneficiario' => $item->proveedor ? $item->proveedor->cuit . ' - ' . $item->proveedor->razon_social : $item->prestador->cuit . ' - ' . $item->prestador->razon_social,
                'proveedor' => optional(optional($item->factura)->razonSocial)->razon_social ?? '',
                'monto' => $item->monto_orden_pago,
                'descripcion' => $item->observaciones,
                'estado' => $item->estado->descripcion_estado,
                'observaciones'=>$item->observaciones
            ];
        });

        return $opas;
    }

    public function headings(): array
    {
        return [
            'ID ORDEN DE PAGO',
            'NUMERO ORDEN DE PAGO',
            'FECHA PROBABLE PAGO',
            'FECHA CONFIRMAR PAGO',
            'N° FACTURA',
            'N° LIQUIDACIÓN',
            'TIPO BENEFICIARIO',
            'PRESTADOR',
            'OBRA SOCIAL',
            'MONTO',
            'DESCRIPCIÓN',
            'ESTADO',
            'OBSERVACIONES'
        ];
    }

    public function styles($excel)
    {
        return [
            'A1:BB1' => ['font' => ['bold' => true]],
        ];
    }
}
