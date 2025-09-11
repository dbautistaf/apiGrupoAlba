<?php

namespace App\Exports;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;

class RecetasExport implements FromCollection, WithHeadings, ShouldAutoSize, WithStyles
{
    /**
     * @return \Illuminate\Support\Collection
     */

    use Exportable;

    protected $desde = null, $hasta = null;

    public function __construct(String  $desde, String  $hasta)
    {
        $this->desde = $desde;
        $this->hasta = $hasta;
    }
    public function collection()
    {

        $query = DB::table('tb_recetas')
            ->join('tb_recetas_detalle', 'tb_recetas.id_receta', '=', 'tb_recetas_detalle.id_receta')
            ->join('tb_padron', 'tb_recetas.id_padron', '=', 'tb_padron.id')
            ->join('tb_farmacias', 'tb_recetas.id_farmacia', '=', 'tb_farmacias.id_farmacia')
            ->join('tb_vademecum', 'tb_vademecum.id_vademecum', '=', 'tb_recetas_detalle.id_vademecum')
            ->join('tb_cobertura', 'tb_cobertura.id_cobertura', '=', 'tb_recetas_detalle.id_cobertura')
            ->join('tb_provincias', 'tb_farmacias.id_provincia', '=', 'tb_provincias.id_provincia')
            ->join('tb_partidos', 'tb_farmacias.id_partido', '=', 'tb_partidos.id_partido')
            ->join('tb_localidad', 'tb_farmacias.id_localidad', '=', 'tb_localidad.id_localidad')
            ->select(
                'tb_recetas.caratula',
                'tb_recetas.origen',
                'tb_recetas.periodo',
                'tb_farmacias.cuit',
                'tb_farmacias.razon_social',
                'tb_farmacias.nombre_fantasia',
                'tb_localidad.nombre as localidad_farmacia',
                'tb_partidos.nombre as partidos_farmacia',
                'tb_provincias.nombre as provincia_farmacia',
                'tb_recetas.colegio',
                'tb_padron.cuil_tit',
                'tb_padron.cuil_benef',
                'tb_padron.dni',
                'tb_padron.nombre',
                'tb_padron.apellidos',
                'tb_recetas.validado',
                'tb_recetas.numero_receta',
                'tb_recetas.fecha_prescripcion',
                'tb_recetas.fecha_receta',
                'tb_vademecum.laboratorio',
                'tb_vademecum.droga',
                'tb_vademecum.accion',
                'tb_vademecum.nombre as nombre_vademecum',
                'tb_vademecum.presentacion',
                'tb_vademecum.troquel',
                'tb_recetas.lote',
                'tb_recetas_detalle.cantidad',
                'tb_recetas_detalle.valor_unitario',
                'tb_recetas_detalle.valor_total',
                'tb_recetas_detalle.cargo_osyc',
                'tb_recetas_detalle.venta_publico',
                'tb_cobertura.cobertura',
                'tb_recetas_detalle.afiliado_total',
                'tb_vademecum.autorizacion_previa',
                'tb_recetas.observaciones  as observaciones_recetas',
                'tb_recetas.numero_validacion',
            )->whereBetween('tb_recetas.fecha_carga', [$this->desde, $this->hasta])->orderBy('tb_recetas.numero_receta')->get();
        foreach ($query as $resultado) {
            // Realizamos el cambio en función del valor de la columna origen
            if ($resultado->origen == 1) {
                $resultado->origen = 'OSPF AMBULATORIO';
            } elseif ($resultado->origen == 2) {
                $resultado->origen = 'OSPF CONVENIO COLECTIVO';
            } elseif ($resultado->origen == 3) {
                $resultado->origen = 'OSPF TRAMITE AUTORIZADO';
            } elseif ($resultado->origen == 4) {
                $resultado->origen = 'OSPFCC AMBULATORIO';
            } elseif ($resultado->origen == 5) {
                $resultado->origen = 'OSPF AMBULATORIO MIXTO';
            } elseif ($resultado->origen == 6) {
                $resultado->origen = 'OSPFCC AMBULATORIO MIXTO';
            } elseif ($resultado->origen == 7) {
                $resultado->origen = 'OSPF PMI';
            } elseif ($resultado->origen == 8) {
                $resultado->origen = 'OSPFCC PMI';
            }elseif ($resultado->origen == 9) {
                $resultado->origen = 'OSPFCC PMI AUTORIZADO';
            }elseif ($resultado->origen == 10) {
                $resultado->origen = 'OSPFCC AUTORIZADO';
            }
        }
        return $query;
    }

    public function headings(): array
    {
        return [
            'caratula',
            'origen',
            'periodo',
            'cuit',
            'razon_social',
            'nombre_fantasia',
            'localidad_farmacia',
            'partidos_farmacia',
            'provincia_farmacia',
            'colegio',
            'cuil_tit',
            'cuil_benef',
            'dni',
            'nombre',
            'apellidos',
            'validado',
            'numero_receta',
            'fecha_prescripcion',
            'fecha_receta',
            'laboratorio',
            'droga',
            'accion',
            'nombre_vademecum',
            'presentacion',
            'troquel',
            'lote',
            'cantidad',
            'valor_unitario',
            'valor_total',
            'cargo_osyc',
            'venta_publico',
            'cobertura',
            'afiliado_total',
            'autorizacion_previa',
            'observaciones_recetas',
            'n° validacion'
        ];
    }
    public function styles($excel)
    {
        return [
            'A1:BB1' => ['font' => ['bold' => true]],
        ];
    }
}
