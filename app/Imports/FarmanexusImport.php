<?php

namespace App\Imports;

use App\Models\FarmanexusModelo;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Date;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithStartRow;

class FarmanexusImport implements ToCollection, WithStartRow
{
    /**
     * @param Collection $collection
     */

    protected $fecha_proceso, $user;
    private $mensaje;

    public function __construct($fecha_proceso, $user)
    {

        $this->fecha_proceso = $fecha_proceso;
        $this->user = $user;
    }

    public function getMensaje()
    {
        return $this->mensaje;
    }

    public function collection(Collection $rows)
    {
        //


        $registrosExistentes = [];
        foreach ($rows as $row) {
            $query = FarmanexusModelo::where('cuf', $row[0])->orWhere('cod_validacion', '=', $row[15])->first();
            if ($query == '') {
                FarmanexusModelo::create([
                    'cuf' => $row[0],
                    'cuit' => $row[1],
                    'razon_social' => $row[2],
                    'nombre_fantasia' => $row[3],
                    'provincia' => $row[4],
                    'fecha_validacion' => \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row[5]),
                    'numero_receta' => $row[6],
                    'nro_item' => $row[7],
                    'nro_afil' => $row[8],
                    'afiliado' => $row[9],
                    'edad' => $row[10],
                    'producto' => $row[11],
                    'cantidad' => $row[12],
                    'precio_venta' => $row[13],
                    'precio_venta_desc' => $row[14],
                    'cod_validacion' => $row[15],
                    'estado' => $row[16],
                    'fecha_receta' => \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row[17]),
                    'ppio_activo' => $row[18],
                    'cobertura' => $row[19],
                    'plan' => $row[20],
                    'tipo_matricula' => $row[21],
                    'numero_matricula' => $row[22],
                    'medico' => $row[23],
                    'registroab' => $row[24],
                    'nrodoc_afiliado' => $row[25],
                    'otro_costo' => $row[26],
                    'laboratorio' => $row[27],
                    'labo_id' => $row[28],
                    'prestador' => $row[29],
                    'presentacion_fcia' => $row[30],
                    'id_externo' => $row[31],
                    'recetario_orig' => $row[32],
                    'fecha_proceso' => $this->fecha_proceso,
                    'id_usuario' => $this->user,
                ]);
            } else {
                $registrosExistentes[] = $row[0]; // Guardar el CUFe de registros existentes
            }
        }
        if (!empty($registrosExistentes)) {
            $this->mensaje = 'Los siguientes CUF ya se registraron anteriormente: ' . implode(', ', $registrosExistentes);
        } else {
            $this->mensaje = 'Todos los datos del archivo se registraron correctamente';
        }
    }

    public function startRow(): int
    {
        return 2;
    }
}
