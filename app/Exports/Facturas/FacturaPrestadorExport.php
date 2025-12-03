<?php

namespace App\Exports\Facturas;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;

class FacturaPrestadorExport implements FromCollection, WithHeadings, ShouldAutoSize, WithStyles
{
    /**
     * @return \Illuminate\Support\Collection
     */
    use Exportable;
    protected $params;

    public function __construct($param)
    {
        $this->params = $param;
    }

    public function collection()
    {
        //
        $sql = "SELECT vwm.cuit, vwm.razon_social, vwm.comprobante, vwm.refacturacion, vwm.delegacion, vwm.periodo, 
               ma.articulo, 
               tfd.cantidad, tfd.precio_neto, 
               tfd.subtotal, tfd.monto_iva, tfd.total_importe,
               vwm.fecha_comprobante,  vwm.fecha_registra, vwm.total_aprobado, vwm.total_facturado,
               vwm.total_debitado, vwm.r_social, vwm.tipo_comprobante,
               vwm.observaciones
        FROM vw_matriz_facturas_prestador AS vwm 
        LEFT JOIN tb_facturacion_detalle tfd ON tfd.id_factura = vwm.id_factura
        LEFT JOIN tb_facturacion_detalle_impuesto tfdi ON tfdi.id_factura = vwm.id_factura
        LEFT JOIN tb_facturacion_detalle_descuento tfdd ON tfdd.id_factura = vwm.id_factura
        LEFT JOIN vw_matriz_articulos ma ON ma.id_articulo = tfd.id_articulo";

        $params = [];
        $where = [];

        if (!empty($this->params->desde) && !empty($this->params->hasta)) {
            $where[] = " vwm.fecha_registra between  ? and ?";
            $params[] = $this->params->desde;
            $params[] = $this->params->hasta;
        }

        if (!empty($this->params->id_tipo_comprobante)) {
            $where[] = "vwm.id_tipo_comprobante = ?";
            $params[] = $this->params->id_tipo_comprobante;
        }

        if (!empty($this->params->locatario)) {
            $where[] = "vwm.id_razon = ?";
            $params[] = $this->params->locatario;
        }

        if (count($where)) {
            $sql .= " WHERE " . implode(" AND ", $where);
        }

        $sql .= " ORDER BY vwm.id_factura";

        $data = DB::select($sql, $params);
        return collect($data);
    }

    public function headings(): array
    {
        return [
            'CUIT',
            'PRESTADOR',
            'COMPROBANTE',
            'REFACTURACION',
            'DELEGACION',
            'PERIODO',
            'ARTICULO',
            'CANTIDAD',
            'PRECIO NETO',
            'SUBTOTAL',
            'TOTAL IVA',
            'TOTAL NETO',
            'FECHA COMPROBANTE',
            'FECHA REGISTRA',
            'TOTAL APROBADO',
            'TOTAL FACTURADO',
            'TOTAL DEBITADO',
            'RAZON SOCIAL',
            'TIPO COMPROBANTE',
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
