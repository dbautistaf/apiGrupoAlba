<?php

namespace App\Exports;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;

class PadronComercialExport implements FromCollection, WithHeadings, ShouldAutoSize, WithStyles
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
        $padron = DB::table('tb_padron_comercial as p')
            ->select([
                'p.id_locatario as locatario',
                'loc.locatorio',
                'p.id_comercial_caja as obra_social',
                'caj.detalle_comercial_caja',
                'p.id_comercial_origen',
                'orig.detalle_comercial_origen',
                'rnos_anterior as origen',
                'p.orden as orden',
                DB::raw("CONCAT(p.nombre, ' ', p.apellidos) as nombres"),
                's.sexo as sexo',
                'p.fe_nac as fe_nacimiento',
                DB::raw("TIMESTAMPDIFF(YEAR, p.fe_nac, CURDATE()) as edad"),
                'p.id_estado_civil',
                'est.estado as estado_civil',
                'p.id_tipo_documento as tipo_doc',
                'p.dni as nro_doc',
                'p.cuil_benef as cuil',
                'p.id_nacionalidad',
                'n.nombre as nacionalidad',
                DB::raw("CONCAT(p.calle, ' ', p.numero, ' ', IFNULL(p.piso, ''), ' ', IFNULL(p.depto, '')) as domicilio"),
                'l.nombre as localidad',
                'p.id_cpostal as codigo_postal',
                'p.telefono',
                'p.email as mail',
                'pa.parentesco',
                'p.cuil_tit as cuil_titular',
                'p.discapacidad as discapacitado',
                'id_regimen as id_tipo_beneficiario',
                'tip.tipo as tipo_beneficiario',
                //'p.fe_alta as fecha_alta_plan',
                'p.fecha_carga as fecha_alta_sss',
                'p.id_agente as promotor',
                'age.nombres_Agente as nombres_promotor',
                'pr.nombre as provincia',
            ])
            ->leftJoin('tb_sexo as s', 'p.id_sexo', '=', 's.id_sexo')
            ->leftJoin('tb_locatorio as loc', 'p.id_locatario', '=', 'loc.id_locatorio')
            ->leftJoin('tb_comercial_caja as caj', 'p.id_comercial_caja', '=', 'caj.id_comercial_caja')
            ->leftJoin('tb_parentesco as pa', 'p.id_parentesco', '=', 'pa.id_parentesco')
            ->leftJoin('tb_localidad as l', 'p.id_localidad', '=', 'l.id_localidad')
            ->leftJoin('tb_comercial_origen as orig', 'p.id_comercial_origen', '=', 'orig.id_comercial_origen')
            ->leftJoin('tb_nacionalidad as n', 'p.id_nacionalidad', '=', 'n.id_nacionalidad')
            ->leftJoin('tb_provincias as pr', 'p.id_provincia', '=', 'pr.id_provincia')
            ->leftJoin('tb_beneficiario as tip', 'p.id_tipo_beneficiario', '=', 'tip.id_tipo_beneficiario')
            ->leftJoin('tb_estado_civil as est', 'p.id_estado_civil', '=', 'est.id_estado_civil')
            ->leftJoin('tb_agentes as age', 'p.id_agente', '=', 'age.id_agente')
            ->where('p.activo', 1)
            ->when(!empty($this->params->desde) && !empty($this->params->hasta), function ($query) {
                $query->whereBetween('fecha_carga', [$this->params->desde, $this->params->hasta]);
            })
            ->get();

        return $padron;
    }

    public function headings(): array
    {
        return [
            'ID LOCATORIO',
            'LOCATORIO',
            'OBRA SOCIAL',
            'COMERCIAL CAJA',
            'ID COMERCIAL ORIGEN',
            'COMERCIAL ORIGEN',
            'ORIGEN',
            'ORDEN',
            'NOMBRES',
            'SEXO',
            'F. NAC',
            'EDAD',
            'ID ESTADO CIVIL',
            'ESTADO CIVIL',
            'TIPO DOC.',
            'NUM DOC',
            'CUIL',
            'ID NACIONALIDAD',
            'NACIONALIDAD',
            'DOMICILIO',
            'LOCALIDAD',
            'COD POSTAL',
            'TELEFONO',
            'MAIL',
            'PARENTESCO',
            'CUIL TIT',
            'DISCAPACIDAD',
            'ID TIPO BENEF',
            'TIPO BENEF',
            'FECH ALTA',
            'PROMOTOR',
            'NOM. PROMOTOR',
            'PROVINCIA',
        ];
    }

    public function styles($excel)
    {
        return [
            'A1:BB1' => ['font' => ['bold' => true]],
        ];
    }
}
