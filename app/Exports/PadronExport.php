<?php

namespace App\Exports;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;

class PadronExport implements FromCollection, WithHeadings, ShouldAutoSize, WithStyles
{
    /**
     * @return \Illuminate\Support\Collection
     */
    use Exportable;
    protected $tipo = [];

    public function __construct($tipo)
    {
        if ($tipo == 3) {
            $this->tipo = [1, 0];
        }else{
            $this->tipo = [$tipo];
        }
    }

    public function collection()
    {
        //
        $query = DB::table('tb_padron')
            ->leftJoin('tb_estado_civil', 'tb_padron.id_estado_civil', '=', 'tb_estado_civil.id_estado_civil')
            ->leftJoin('tb_nacionalidad', 'tb_padron.id_nacionalidad', '=', 'tb_nacionalidad.id_nacionalidad')
            ->leftJoin('tb_localidad', 'tb_padron.id_localidad', '=', 'tb_localidad.id_localidad')
            ->leftJoin('tb_situacion_revista', 'tb_padron.id_situacion_de_revista', '=', 'tb_situacion_revista.id_situacion_de_revista')
            ->leftJoin('tb_relacion_labora', 'tb_padron.dni', '=', 'tb_relacion_labora.id_padron')
            ->leftJoin('tb_empresa', 'tb_empresa.id_empresa', '=', 'tb_relacion_labora.id_empresa')
            ->leftJoin('tb_discapaciodad', 'tb_padron.id', '=', 'tb_discapaciodad.id_padron')
            ->leftJoin('tb_locatorio', 'tb_padron.id_locatario', '=', 'tb_locatorio.id_locatorio')
            ->leftJoin('tb_comercial_caja', 'tb_padron.id_comercial_caja', '=', 'tb_comercial_caja.id_comercial_caja')
            ->leftJoin('tb_comercial_origen', 'tb_padron.id_comercial_origen', '=', 'tb_comercial_origen.id_comercial_origen')
            ->leftJoin('tb_detalle_padron_tipo_plan', 'tb_padron.dni', '=', 'tb_detalle_padron_tipo_plan.id_padron')
            ->leftJoin('tb_tipo_plan', 'tb_tipo_plan.id_tipo_plan', '=', 'tb_detalle_padron_tipo_plan.id_tipo_plan')
            ->select(
                'tb_empresa.cuit',
                'tb_padron.cuil_tit',
                'tb_padron.id_parentesco',
                'tb_padron.cuil_benef',
                'tb_padron.id_tipo_documento',
                'tb_padron.dni',
                DB::raw('CONCAT(tb_padron.apellidos, " ", tb_padron.nombre) as nombre_padron'),
                'tb_padron.id_sexo',
                'orden_grupo',
                'tb_estado_civil.estado',
                DB::raw("CONCAT(SUBSTR(tb_padron.fe_nac, 9, 2), '/', SUBSTR(tb_padron.fe_nac, 6, 2), '/', SUBSTR(tb_padron.fe_nac, 1, 4)) AS fecnac"),
                'tb_nacionalidad.Nombre',
                'tb_padron.calle',
                'tb_padron.numero',
                'tb_padron.piso',
                'tb_padron.depto',
                'tb_localidad.nombre as localidad',
                'tb_padron.id_cpostal',
                'tb_padron.id_provincia',
                'tb_padron.id_tipo_domicilio',
                'tb_padron.telefono',
                'tb_situacion_revista.situacion',
                DB::raw('CASE WHEN tb_discapaciodad.id_padron IS NULL THEN "NO" ELSE "SI" END AS incapacidad'),
                'tb_padron.id_tipo_beneficiario',
                DB::raw("CONCAT(SUBSTR(tb_padron.fe_alta, 9, 2), '/', SUBSTR(tb_padron.fe_alta, 6, 2), '/', SUBSTR(tb_padron.fe_alta, 1, 4)) AS fe_alta"),
                'tb_locatorio.locatorio',
                'tb_comercial_caja.detalle_comercial_caja as caja',
                'tb_comercial_origen.detalle_comercial_origen as origen',
                DB::raw('TIMESTAMPDIFF(YEAR, tb_padron.fe_nac, CURDATE()) as edad'),
                'tb_padron.email',
                'tb_tipo_plan.tipo as plan'
            )->whereIn('tb_padron.activo', $this->tipo)->orderBy('tb_padron.cuil_tit', 'desc')->orderBy('tb_padron.id_parentesco', 'asc')->get();
        return $query;
    }

    public function headings(): array
    {
        return [
            'CUIT',
            'CUIL TITULAR',
            'PARENTESCO',
            'CUIL BENEF',
            'TIPO DOCUMENTO',
            'DNI',
            'NOMBRES',
            'SEXO',
            'ORDEN',
            'ESTADO CIVIL',
            'FECHA NACIMIENTO',
            'NACIONALIDAD',
            'CALLE',
            'NUMERO',
            'PISO',
            'DEPTO',
            'LOCALIDAD',
            'C_POSTAL',
            'ID PROVINCIA',
            'TIPO DOMICILIO',
            'TELEFONO',
            'SITUACION REVISTA',
            'INCAPACIDAD',
            'TIPO BENEFICIARIO',
            'FECHA DE ALTA',
            'MARCA',
            'CAJA',
            'ORIGEN',
            'EDAD',
            'EMAIL',
            'PLAN'
        ];
    }

    public function styles($excel)
    {
        return [
            'A1:BB1' => ['font' => ['bold' => true]],
        ];
    }
}
