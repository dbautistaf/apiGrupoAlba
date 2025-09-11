<?php

namespace App\Imports;

use App\Models\pratricaMatriz\PracticaNomencladorEntity;
use Illuminate\Support\Collection;
use App\Models\pratricaMatriz\PracticaMatrizEntity;
use App\Models\pratricaMatriz\PracticaPadreEntity;
use App\Models\pratricaMatriz\PracticaSeccionesEntity;
use App\Models\pratricaMatriz\PracticaTipoGalenoEntity;
use App\Models\pratricaMatriz\PracticaValorizacionEntity;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithStartRow;

class PracticasMatrizImport implements ToCollection, WithStartRow
{

    public $idDefault;

    public function __construct($idDefault)
    {
        $this->idDefault = $idDefault;
    }

    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            // if (!empty($row[3]) && !empty($row[7]) &&  !empty($row[9]) && !empty($row[13]) && !empty($row[1])) {
            $nomenclador = PracticaNomencladorEntity::where('descripcion', $row[0])->first();
            $session = PracticaSeccionesEntity::where('descripcion', $row[3])->where('id_nomenclador', $nomenclador->id_nomenclador)->first();
            $padre = PracticaPadreEntity::where('descripcion', $row[7])->first();
           // $categoria = InternacionesCategoriaInternacionEntity::where('descripcion', $row[9])->first();
            $valorizacion = PracticaValorizacionEntity::where('descripcion', $row[12])->first();
            $galeno = PracticaTipoGalenoEntity::where('descripcion', $row[13])->first();

            PracticaMatrizEntity::create([
                'codigo_practica' => $row[1],
                'id_seccion' => $session != null ? $session->id_seccion :  $this->idDefault,
                'nombre_practica' => $row[2],
                'cobertura' => $row[4],
                'coseguro' => $row[5],
                'vigente' => '1',
                'norma' => $row[8],
                'id_padre' => $padre != null ? $padre->id_padre : null,
                'cod_categoria_internacion' => null,//$categoria != null ? $categoria->cod_categoria_internacion : null,
                'id_practica_valorizacion' => $valorizacion != null ? $valorizacion->id_practica_valorizacion : null,
                'id_tipo_galeno' => $galeno != null ? $galeno->id_tipo_galeno : null,
                'especialista' => $row[14],
                'ayudante_cantidad' => $row[15],
                'ayudante' => $row[16],
                'anestesista' => $row[17],
                'galeno_gasto' => $row[18],
                'valor_gasto' => $row[19],
                'galeno_adicional' => $row[20],
                'valor_adicional' => $row[21],
                'galeno_aparatologia' => $row[22],
                'valor_aparatologia' => $row[23],
                'fecha_vigencia' => null,
            ]);
            // }
        }
    }

    public function startRow(): int
    {
        return 2;
    }
}
