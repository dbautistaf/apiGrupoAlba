<?php

namespace App\Imports;

use App\Models\TransaccionesDetalleModel;
use App\Models\TransaccionesModel;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithStartRow;

class TransaccionesImport implements ToCollection, WithStartRow
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
            $query = TransaccionesModel::where('id_autorizacion', $row[0])->first();
            if ($query == '') {
                $query2 = TransaccionesModel::where('id_autorizacion', $row[0])->where('fecha_carga', $this->fecha_proceso)->first();
                if ($query2 == '') {
                    $transaccion = TransaccionesModel::create([
                        'id_autorizacion' => $row[0],
                        'fecha_receta' => $row[1],
                        'fecha_venta' => $row[2],
                        'plan' => $row[3],
                        'nro_receta' => $row[4],
                        'cuil' => $row[5],
                        'nombre_afiliado' => $row[6],
                        'matricula_medico' => $row[7],
                        'nombre_medico' => $row[8],
                        'diagnostico' => $row[9],
                        'nombre_farmacia' => $row[10],
                        'cuit' => $row[11],
                        'localidad' => $row[12],
                        'fecha_carga' => $this->fecha_proceso,
                        'id_usuario' => $this->user,
                    ]);

                    TransaccionesDetalleModel::create([
                        'linea' => $row[13],
                        'registro' => $row[14],
                        'troquel' => $row[15],
                        'nombre' => $row[16],
                        'cantidad' => $row[17],
                        'cobertura' => $row[18],
                        'precio_vigente' => $row[19],
                        'id_transacciones' => $transaccion->id_transacciones
                    ]);
                } else {
                    TransaccionesDetalleModel::create([
                        'linea' => $row[13],
                        'registro' => $row[14],
                        'troquel' => $row[15],
                        'nombre' => $row[16],
                        'cantidad' => $row[17],
                        'cobertura' => $row[18],
                        'precio_vigente' => $row[19],
                        'id_transacciones' => $query->id_transacciones
                    ]);
                }
            } else {
            }
        }

        /* if (!empty($registrosExistentes)) {
            $this->mensaje = 'Los siguientes CUF se ya se registraron anteriormente: ' . implode(', ', $registrosExistentes);
        } else {
            $this->mensaje = 'Todos los datos del archivo se registraron correctamente';
        } */

        $this->mensaje = 'Datos de archivo registrado correctamente';
    }

    public function startRow(): int
    {
        return 2;
    }
}
