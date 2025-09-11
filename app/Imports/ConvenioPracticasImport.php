<?php

namespace App\Imports;

use App\Exports\PracticasNoEncontradasExport;
use App\Models\convenios\ConvenioHistorialCostosPracticaEntity;
use App\Models\convenios\ConveniosPracticasEntity;
use App\Models\pratricaMatriz\PracticaMatrizEntity;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Illuminate\Support\Collection;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;

class ConvenioPracticasImport implements ToCollection, WithStartRow
{
    public $practicasNoEncontradas = [];
    public $idConvenio;
    public $fecha_contrato;
    public $fecha_fin_contrato;


    public function __construct($convenio, $fecha_contrato, $fecha_fin_contrato)
    {
        $this->idConvenio = $convenio;
        $this->fecha_contrato = $fecha_contrato;
        $this->fecha_fin_contrato = $fecha_fin_contrato;
    }

    public function collection(Collection $rows)
    {
        $user = Auth::user();
        $fechaActual = Carbon::now('America/Argentina/Buenos_Aires');
        foreach ($rows as $row) {
            if (!is_null($row[3])) {
                $practica = PracticaMatrizEntity::where('codigo_practica', $row[0])->first();
                if (!is_null($practica)) {
                    if (
                        !ConveniosPracticasEntity::where('id_identificador_practica', $practica->id_identificador_practica)
                            ->where('cod_convenio', $this->idConvenio)
                            ->whereDate('fecha_vigencia', $this->transformDate($row[3]))
                            //->where('monto_gastos', $row[2])
                            ->where('tipo_carga', 'Gasto')
                            ->where('vigente', '1')
                            ->exists()
                    ) {
                        $practicaExisteConvenio = ConveniosPracticasEntity::where('id_identificador_practica', $practica->id_identificador_practica)
                            ->where('cod_convenio', $this->idConvenio)
                            ->where('vigente', '1')
                            ->orderByDesc('id_practica_convenio')
                            ->limit(1)
                            ->first();

                        ConveniosPracticasEntity::create([
                            'id_identificador_practica' => $practica->id_identificador_practica,
                            'cod_convenio' => $this->idConvenio,
                            'monto_especialista' => 0,
                            'monto_gastos' =>   $this->extraerNumero($row[2]),
                            'monto_ayudante' => 0,
                            'vigente' => '1',
                            'tipo_carga' => 'Gasto',
                            'fecha_vigencia' => $this->transformDate($row[3]),
                            'fecha_carga' => $fechaActual,
                            'cod_usuario_carga' => $user->cod_usuario,
                            'fecha_vigencia_hasta' => $this->fecha_fin_contrato
                        ]);

                        $valorAnterior = 0;
                        if (!is_null($practicaExisteConvenio)) {
                            $practicaExisteConvenio->vigente = '0';
                            $practicaExisteConvenio->fecha_vigencia_hasta = $this->restarUnDia($this->transformDate($row[3]));
                            $practicaExisteConvenio->update();
                            $valorAnterior = $practicaExisteConvenio->monto_gastos;
                        }

                        $historialExiste = ConvenioHistorialCostosPracticaEntity::where('id_identificador_practica', $practica->id_identificador_practica)
                            ->where('cod_convenio', $this->idConvenio)
                            ->where('vigente', '1')
                            ->orderByDesc('id_historial_pago')
                            ->limit(1)
                            ->first();

                        if (!is_null($historialExiste)) {
                            $historialExiste->fecha_fin = $this->restarUnDia($this->transformDate($row[3]));
                            $historialExiste->vigente = '0';
                            $historialExiste->cod_usuario_update = $user->cod_usuario;
                            $historialExiste->update();
                        }

                        ConvenioHistorialCostosPracticaEntity::create([
                            'id_identificador_practica' => $practica->id_identificador_practica,
                            'cod_convenio' => $this->idConvenio,
                            'monto_especialista' => 0,
                            'monto_gastos' => $this->extraerNumero($row[2]),
                            'monto_ayudante' => 0,
                            'vigente' => '1',
                            'tipo_carga' => 'IMPORT_COSTO',
                            'fecha_inicio' => $this->transformDate($row[3]),
                            'fecha_fin' => $this->fecha_fin_contrato,
                            'fecha_update' => $fechaActual,
                            'cod_usuario_crea' => $user->cod_usuario,
                            'valor_aumento_lineal' => $this->calcularIncremento($valorAnterior, $row[2]),
                            'tipo_aumento' => ($valorAnterior > 0 ? 'LINEAL - IMPORTADOR' : 'VALOR INICIAL')
                        ]);
                    } else {
                        $this->practicasNoEncontradas[] = [
                            'codigo_practica' => $row[0],
                            'descripcion_practica' => $row[1],
                            'valor' => $row[2],
                            'vigencia' => $row[3],
                            'obs' => 'Ya éxiste un registro con estos valores: FECHA VIGENCIA | PRACTICA | COSTO PRACTICA. Es posible que el archivo aya sido importado anteriormente'
                        ];
                    }
                } else {
                    $this->practicasNoEncontradas[] = [
                        'codigo_practica' => $row[0],
                        'descripcion_practica' => $row[1],
                        'valor' => $row[2],
                        'vigencia' => $row[3],
                        'obs' => 'El codigo de practica no existe'
                    ];
                }
            } else {
                $this->practicasNoEncontradas[] = [
                    'codigo_practica' => $row[0],
                    'descripcion_practica' => $row[1],
                    'valor' => $row[2],
                    'vigencia' => $row[3],
                    'obs' => 'El campo vigencia no puede ser vacio.'
                ];
            }
        }

        if (!empty($this->practicasNoEncontradas)) {
            $this->generateLog();
        }
    }

    public function startRow(): int
    {
        return 2;
    }

    public function generateLog()
    {
        $filePath = 'public/logs/logs_imports_' . $this->idConvenio . '.xlsx';
        Excel::store(new PracticasNoEncontradasExport($this->practicasNoEncontradas), $filePath);
    }
    /*  public function transformDate($value)
     {
         try {
             return Carbon::instance(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($value))->format('Y-m-d');
         } catch (\ErrorException $e) {
             return Carbon::parse($value)->format('Y-m-d');
         }
     } */


    public function transformDate($value)
    {
        try {
            // Verifica si el valor es numérico (indica una fecha en formato de Excel)
            if (is_numeric($value)) {
                return Carbon::instance(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($value))->format('Y-m-d');
            } else {
                if (Carbon::hasFormat($value, 'Y-m-d')) {
                    return Carbon::createFromFormat('Y-m-d', $value)->format('Y-m-d');
                } elseif (Carbon::hasFormat($value, 'd-m-Y')) {
                    return Carbon::createFromFormat('d-m-Y', $value)->format('Y-m-d');
                } else {
                    throw new \Exception("El formato de fecha no es válido: $value");
                }
            }
        } catch (\Exception $e) {
            // Manejo de excepciones si el formato de fecha no es válido
            return null; // O maneja el error según tu necesidad
        }
    }

    public function calcularIncremento($valorAntiguo, $valorNuevo)
    {
        if ($valorAntiguo == 0) {
            return 0;
        }
        $incremento = (($valorNuevo - $valorAntiguo) / $valorAntiguo) * 100;
        return round($incremento, 2);
    }


    function sumarUnDia($fecha)
    {
        $fechaCarbon = Carbon::parse($fecha);
        $fechaCarbon->addDay();
        return $fechaCarbon->format('Y-m-d');
    }

    function restarUnDia($fecha)
    {
        $fechaCarbon = Carbon::parse($fecha);
        $fechaCarbon->subDay();
        return $fechaCarbon->format('Y-m-d');
    }

    private function extraerNumero($valor)
    {

        $limpio = preg_replace('/[^\d.]/', '', $valor);


        return number_format((float) $limpio, 2, '.', '');
    }

}
