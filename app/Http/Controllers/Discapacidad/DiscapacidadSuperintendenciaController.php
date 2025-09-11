<?php

namespace App\Http\Controllers\Discapacidad;

use App\Exports\SubsidiosExport;
use App\Http\Controllers\Discapacidad\Repository\DiscapacidadRepository;
use App\Models\DiscaPacidadDetalleModel;
use App\Models\IntegracionCodigoErroresModel;
use App\Models\IntegracionDiscapacidadModel;
use App\Models\SubsidiosDiscapacidadModel;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class DiscapacidadSuperintendenciaController extends Controller
{

    public function srvRegistrarDiscapacidad(DiscapacidadRepository $repo, Request $request)
    {
        DB::beginTransaction();
        $user = Auth::user();

        try {
            $message = "";
            $request->merge(['cod_usuario' => $user->cod_usuario]);

            if (!is_null($request->id_discapacidad)) {
                $model = IntegracionDiscapacidadModel::find($request->id_discapacidad);
                $model->update($request->all());

                DB::delete("DELETE FROM tb_discapacidad_detalle WHERE id_discapacidad = ? ", [$request->id_discapacidad]);
                foreach ($request->detalle as $value) {
                    if (is_null($value["dependencia"])) {
                        DB::rollBack();
                        return response()->json(["message" => "Ingrese un valor de <b>DEPENDENCIA</b> para la prestación <b>" . $value["id_practica"] . "</b>"], 409);
                    }
                    $repo->findBySaveDetalle($value, $request->id_discapacidad);
                }
                $message = "Los datos fueron actualizados con éxito.";
            } else {
                /* VALIDAR QUE NO SE REGISTRE LA MISMA BOLETA | NUM COMPROBANTE | CAE CAI | CUIT | PERIODO  | */
                $existsBoleta = $repo->findByExistsBoleta($request);
                if ($existsBoleta) {
                    DB::rollBack();
                    return response()->json(["message" => "Esta registro ya existe en nuestra base de datos."], 409);
                }

                $disca = IntegracionDiscapacidadModel::create($request->all());

                $disca->refresh();
                foreach ($request->detalle as $value) {
                    if (is_null($value["dependencia"])) {
                        DB::rollBack();
                        return response()->json(["message" => "Ingrese un valor de <b>DEPENDENCIA</b> para la prestación <b>" . $value["id_practica"] . "</b>"], 409);
                    }
                    $repo->findBySaveDetalle($value, $disca->id_discapacidad);
                }
                $message = "Los datos fueron guardados con éxito.";
            }


            DB::commit();

            return response()->json([
                "message" => $message
            ], 200);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(["message" => $th->getMessage()], 500);
        }
    }

    public function srvListarData(Request $request)
    {
        try {
            $data = [];
            if (!is_null($request->cuil_benef)) {
                $data = DiscaPacidadDetalleModel::with(['practica', 'disca', 'disca.afiliado', 'disca.usuario'])
                    ->whereHas('disca', function ($query) use ($request) {
                        $query->where('cuil_beneficiario', $request->cuil_benef);
                    })
                    //->where('subsidio', '0')
                    ->orderByDesc('id_discapacidad_detalle')
                    ->limit(2000)
                    ->get();
            } else if (!is_null($request->cuil_prestador)) {
                $data = DiscaPacidadDetalleModel::with(['practica', 'disca', 'disca.afiliado', 'disca.usuario'])
                    ->whereHas('disca', function ($query) use ($request) {
                        $query->where('cuil_prestador', $request->cuil_prestador);
                    })
                    //->where('subsidio', '0')
                    ->orderByDesc('id_discapacidad_detalle')
                    ->limit(2000)
                    ->get();
            } else if (!is_null($request->nombre_benef)) {
                $data = DiscaPacidadDetalleModel::with(['practica', 'disca', 'disca.afiliado', 'disca.usuario'])
                    ->whereHas('disca.afiliado', function ($query) use ($request) {
                        $query->where('apellidos', 'like', '%' . $request->nombre_benef . '%')
                            ->orWhere('nombre', 'like', '%' . $request->nombre_benef . '%');
                    })
                    //->where('subsidio', '0')
                    ->orderByDesc('id_discapacidad_detalle')
                    ->limit(2000)
                    ->get();
            } else if (!is_null($request->num_factura)) {
                $data = DiscaPacidadDetalleModel::with(['practica', 'disca', 'disca.afiliado', 'disca.usuario'])
                    ->whereHas('disca', function ($query) use ($request) {
                        $query->where('num_factura', $request->num_factura);
                    })
                    //->where('subsidio', '0')
                    ->orderByDesc('id_discapacidad_detalle')
                    ->limit(2000)
                    ->get();
            } else if (!is_null($request->cod_pratica)) {
                $data = DiscaPacidadDetalleModel::with(['practica', 'disca', 'disca.afiliado', 'disca.usuario'])
                    ->where('id_practica', $request->cod_pratica)
                    //->where('subsidio', '0')
                    ->orderByDesc('id_discapacidad_detalle')
                    ->limit(2000)
                    ->get();
            } else if (!is_null($request->cae_cai)) {
                $data = DiscaPacidadDetalleModel::with(['practica', 'disca', 'disca.afiliado', 'disca.usuario'])
                    ->whereHas('disca', function ($query) use ($request) {
                        $query->where('num_cae_cai', $request->cae_cai);
                    })
                    //->where('subsidio', '0')
                    ->orderByDesc('id_discapacidad_detalle')
                    ->limit(2000)
                    ->get();
            } else {
                $data = DiscaPacidadDetalleModel::with(['practica', 'disca', 'disca.afiliado', 'disca.usuario'])
                    // ->where('subsidio', '0')
                    ->orderByDesc('id_discapacidad_detalle')
                    ->limit(2000)
                    ->get();
            }


            return response()->json($data, 200);
        } catch (\Throwable $th) {
            return response()->json(["message" => $th->getMessage()], 500);
        }
    }
    public function generarArchivo(Request $request)
    {
        $data = [];
        $data = DiscaPacidadDetalleModel::with(['practica', 'disca', 'disca.afiliado'])
            ->whereHas('disca', function ($query) use ($request) {
                $query->whereBetween('periodo_prestacion', [str_replace('-', '', $request->desde), str_replace('-', '', $request->hasta)]);
                $query->whereBetween('fecha_registra', [$request->cargaDesde, $request->cargaHasta]);
                $query->whereNotIn('cod_usuario', [1]);
            })
            //->whereBetween('periodo_prestacion',[str_replace('-', '', $request->desde), str_replace('-', '', $request->hasta)])
            ->get();
        $contenido = "";
        foreach ($data as $key) {
            // TIPO DE ARCHIVO (2) | CODIGO OBRA SOCIAL (6)
            $contenido .= "DS|107404|";
            // CUIL BENEFICIARIO (11)
            $contenido .= $key->disca->cuil_beneficiario . '|';
            // CODIGO CERTIFICADO (33) DIGITOS  - COMPLETAR CON ESPACIOS
            $contenido .= $this->completarConEspacios($key->disca->codigo_certificado, 33) . '|';
            // VENCIMIENTO CERTIFICADO (10)
            $contenido .= date('d/m/Y', strtotime($key->disca->vnto_certificado)) . '|';
            // PERIODO PRESTACION (6)
            $contenido .= $key->disca->periodo_prestacion . '|';
            // CUIL PRESTADO (11)
            $contenido .= $key->disca->cuil_prestador . '|';
            // TIPO COMPROBANTE (2) - prácticas 97, 98 y 99 se deberá informar “00”
            $contenido .= $key->disca->id_tipo_comprobante . '|';
            // TIPO EMISION (1) -  prácticas 97, 98 y 99 se deberá informar “N”
            $contenido .= $key->disca->id_tipo_emision . '|';
            // FECHA DE EMISION (10) - prácticas 97, 98 y 99 se deberán informar la fecha del periodo de prestación
            if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $key->disca->fecha_emision_comprobante)) {
                $contenido .= date('d/m/Y', strtotime($key->disca->fecha_emision_comprobante)) . '|';
            } else {
                $contenido .= $key->disca->fecha_emision_comprobante . '|';
            }
            // NUMERO DE CAE CAI (14) - COMPLETAR CON 0 A LA IZQUIERDA - prácticas 97, 98 y 99 se deberá completar con 14 dígitos en “0”
            $contenido .= $this->completarConCeros($key->disca->num_cae_cai, 14) . '|';
            // PUNTO VENTA (5) - COMPLETAR CON 0 A ALA IZQUIERDA - prácticas 97, 98 y 99 se deberá completar con 5 dígitos en “0”
            $contenido .= $this->completarConCeros($key->disca->punto_venta, 5) . '|';
            // NUMERO COMPROBANTE (8) - COMPLETAR CON 0 A LA IZQUIERDA - prácticas 97, 98 y 99 se deberá completar con 8 dígitos en “0”
            $contenido .= $this->completarConCeros($key->disca->num_comprobante, 8) . '|';
            // IMPORTE COMPROBANTE (14) - COMPLETAR CON 0 A ALA IZQUIERDA - práctica 97, 98 y 99 se deberá completar con 10 dígitos en “0”
            $contenido .= $this->completarConCeros(str_replace('.', '', $key->disca->monto_comprobante), 14) . '|';
            // IMPORTE SOLICITADO (14) - COMPLETAR CON 0 A ALA IZQUIERDA - práctica 97, 98 y 99 se deberá completar con 10 dígitos en “0”
            $contenido .= $this->completarConCeros(str_replace('.', '', $key->disca->monto_solicitado), 14) . '|';
            // CODIGO DE PRACTICA (3)
            $contenido .= $key->id_practica . '|';
            // CANTIDAD (5) - COMPLETAR CON 0 A AL IZQUIERDA
            $contenido .= $this->completarConCeros($key->cantidad, 5) . '|';
            // PROVINCIA (2)
            $contenido .= $key->disca->id_provincia_discapacidad . '|';
            // DEPENDENCIA (1) - SI O NO
            $contenido .= $key->dependencia;
            // SALTO DE LINEA PARA UNA NUEVA FILA
            $contenido .= "\n";
        }

        $nombreArchivo = '107404_ds.txt';

        $response = Response::make($contenido);

        $response->header('Content-Type', 'text/plain');

        $response->header('Content-Disposition', 'attachment; filename="' . $nombreArchivo . '"');

        return $response;
    }

    public function srvImportarArchivosOK(Request $request)
    {
        try {
            //DB::beginTransaction();
            if ($request->hasFile('archivo')) {
                $user = Auth::user();
                $fechaActual = Carbon::now('America/Argentina/Buenos_Aires');
                $archivo = $request->file('archivo');
                $contenido = file_get_contents($archivo->getRealPath());
                $lineas = explode("\n", $contenido);
                $data = array();
                foreach ($lineas as $linea) {
                    $campos = explode('|', $linea);
                    if (isset($campos[1])) {
                        if ($campos[0] !== 'DS') {
                            return response()->json(["message" => "El archivo seleccionado no se corresponde con el formato de Superintendencia"], 409);
                        }

                        $prestacion = DiscaPacidadDetalleModel::with(['practica', 'disca', 'disca.afiliado'])
                            ->whereHas('disca', function ($query) use ($campos) {
                                $query->where('cuil_beneficiario', [$campos[2]])
                                    ->where('num_comprobante', [$campos[12]])
                                    ->where('num_cae_cai', [$campos[10]])
                                    ->where('periodo_prestacion', [$campos[5]]);
                            })
                            ->where('id_practica', [$this->completarConCeros($campos[15], 3)])
                            ->where('subsidio', 0)
                            ->get();

                        if (count($prestacion) > 0) {
                            $data[] = $prestacion[0];
                        }
                    }
                }
            }
            //  DB::commit();
            return response()->json($data, 200);
        } catch (\Throwable $th) {
            // DB::rollBack();
            return response()->json(["message" => $th->getMessage()], 500);
        }
    }
    public function srvCargaMasivaFacturas(Request $request)
    {
        DB::beginTransaction();
        try {
            $user = Auth::user();
            $fechaActual = Carbon::now('America/Argentina/Buenos_Aires');

            if ($request->hasFile('archivo')) {

                $archivo = $request->file('archivo');
                $contenido = file_get_contents($archivo->getRealPath());
                $lineas = explode("\n", $contenido);
                $x = 0;
                foreach ($lineas as $linea) {
                    $campos = explode('|', $linea);
                    if (isset($campos[1])) {

                        $prestadores = DB::select('select * from tb_provedores_discapacidad where cuit = ? ', [$campos[6]]);
                        $razonSocial = "-";
                        if (count($prestadores) > 0) {
                            $razonSocial = $prestadores[0]->razon_social;
                        }
                        $disca = IntegracionDiscapacidadModel::create([
                            'cuil_beneficiario' => $campos[2],
                            'codigo_certificado' => $campos[3],
                            'vnto_certificado' => substr($campos[4], 6, 4) . '-' . substr($campos[4], 3, 2) . '-' . substr($campos[4], 0, 2),
                            'periodo_prestacion' => $campos[5],
                            'cuil_prestador' => $campos[6],
                            'id_tipo_comprobante' => $campos[7],
                            'id_tipo_emision' => $campos[8],
                            'fecha_emision_comprobante' => substr($campos[9], 6, 4) . '-' . substr($campos[9], 3, 2) . '-' . substr($campos[9], 0, 2),
                            'num_cae_cai' => $campos[10],
                            'punto_venta' => $campos[11],
                            'num_comprobante' => $campos[12],
                            'monto_comprobante' => substr($campos[13], 0, 12) . '.' . substr($campos[13], 12, 14),
                            'monto_solicitado' => substr($campos[14], 0, 12) . '.' . substr($campos[14], 12, 14),
                            'dependencia' => $campos[18],
                            'cod_usuario' => $user->cod_usuario,
                            'fecha_registra' => $fechaActual,
                            'id_provincia_discapacidad' => strlen($campos[17]) == 1 ? '0' . $campos[17] : $campos[17],
                            'num_factura' => $campos[12],
                            'codigo' => $campos[0],
                            'modulo' => '-',
                            'razon_social_prestador' => $razonSocial,
                            'categoria' => '-'
                        ]);

                        $disca->refresh();
                        DiscaPacidadDetalleModel::create([
                            'id_practica' => $this->completarConCeros($campos[15], 3),
                            'cantidad' => $campos[16],
                            'dependencia' => $campos[18],
                            'id_discapacidad' => $disca->id_discapacidad,
                            'subsidio' => 0
                        ]);
                        $x++;
                    }
                }
            }

            DB::commit();
            return response()->json(["message" => $x . " Facturas importadas correctamente"], 200);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(["message" => $th->getMessage()], 500);
        }
    }
    public function srvImportarArchivosErroneos(Request $request)
    {
        try {
            if ($request->hasFile('archivo')) {
                $archivo = $request->file('archivo');
                $contenido = file_get_contents($archivo->getRealPath());
                $lineas = explode("\n", $contenido);
                $valor = null;
                $data = array();
                $x = 0;
                foreach ($lineas as $linea) {
                    $campos = explode('|', $linea);
                    if (isset($campos[1])) {

                        /* if ($campos[0] !== 'DS' && $x > 0) {
                            return response()->json(["message" => "El archivo seleccionado no se corresponde con el formato de Superintendencia"], 409);
                        } */
                        $prestacion = DiscaPacidadDetalleModel::with(['practica', 'disca', 'disca.afiliado'])
                            ->whereHas('disca', function ($query) use ($campos) {
                                $query->where('cuil_beneficiario', [$campos[2]])
                                    ->where('num_comprobante', [$campos[12]])
                                    ->where('num_cae_cai', [$campos[10]])
                                    ->where('periodo_prestacion', [$campos[5]]);
                            })
                            ->where('subsidio', 0)
                            ->where('id_practica', [$campos[15]])
                            ->get();

                        if (count($prestacion) > 0) {

                            $obs = IntegracionCodigoErroresModel::where('cod_error', str_replace('-', '', $campos[19]))->first();

                            $objetop = $prestacion[0];
                            $objetop['observacion'] = $obs;
                            $data[] = $objetop;
                        }
                    }
                    $x++;
                }
            }

            return response()->json($data, 200);
        } catch (\Throwable $th) {
            return response()->json(["message" => $th->getMessage()], 500);
        }
    }

    public static function completarConEspacios($cadena, $longitud)
    {
        $espaciosNecesarios = $longitud - strlen($cadena);
        if ($espaciosNecesarios > 0) {
            $cadena .= str_repeat(' ', $espaciosNecesarios);
        }

        return $cadena;
    }

    public static function completarConCeros($cadena, $longitud)
    {
        $cerosNecesarios = $longitud - strlen($cadena);
        if ($cerosNecesarios > 0) {
            $cadena = str_repeat('0', $cerosNecesarios) . $cadena;
        }

        return $cadena;
    }
    public static function onFiltersSubsidios(Request $request)
    {

        try {
            $subsidios = [];

            if (!is_null($request->cuil_benf)) {
                $subsidios = DiscaPacidadDetalleModel::with(['practica', 'subsidiodisca', 'disca', 'disca.afiliado'])
                    ->whereHas('disca', function ($query) use ($request) {
                        $query->where('cuil_beneficiario', $request->cuil_benf);
                    })
                    ->where('subsidio', 1)
                    ->orderByDesc('id_discapacidad_detalle')
                    ->limit(2000)
                    ->get();
            } else if (!is_null($request->cuil_prestador)) {
                $subsidios = DiscaPacidadDetalleModel::with(['practica', 'subsidiodisca', 'disca', 'disca.afiliado'])
                    ->whereHas('disca', function ($query) use ($request) {
                        $query->where('cuil_prestador', $request->cuil_prestador);
                    })
                    ->where('subsidio', 1)
                    ->orderByDesc('id_discapacidad_detalle')
                    ->limit(2000)
                    ->get();
            } else if (!is_null($request->periodo)) {
                $subsidios = DiscaPacidadDetalleModel::with(['practica', 'subsidiodisca', 'disca', 'disca.afiliado'])
                    ->whereHas('disca', function ($query) use ($request) {
                        $query->where('periodo_prestacion', $request->periodo);
                    })
                    ->where('subsidio', 1)
                    ->orderByDesc('id_discapacidad_detalle')
                    ->limit(2000)
                    ->get();
            } else if (!is_null($request->factura)) {
                $subsidios = DiscaPacidadDetalleModel::with(['practica', 'subsidiodisca', 'disca', 'disca.afiliado'])
                    ->whereHas('disca', function ($query) use ($request) {
                        $query->where('num_factura', $request->factura);
                    })
                    ->where('subsidio', 1)
                    ->orderByDesc('id_discapacidad_detalle')
                    ->limit(2000)
                    ->get();
            } else {
                $subsidios = DiscaPacidadDetalleModel::with(['practica', 'subsidiodisca', 'disca', 'disca.afiliado', 'disca.prestador'])
                    ->where('subsidio', 1)
                    ->orderByDesc('id_discapacidad_detalle')
                    ->limit(2000)
                    ->get();
            }

            return response()->json($subsidios, 200);
        } catch (\Throwable $th) {
            return response()->json(["message" => $th->getMessage()], 500);
        }
    }

    public function export(Request $request)
    {
        return Excel::download(new SubsidiosExport($request), 'subsidios.xlsx');
    }

    public function srvProcesarPresupuesto(Request $request)
    {
        $nombreArchivo = "";
        try {
            $fechaActual = Carbon::now('America/Argentina/Buenos_Aires');

            if ($request->hasFile('archivo')) {
                $data = json_decode($request->data);
                $file = $request->file('archivo');
                $nombreArchivo = "FILE_PRESUPUESTO_" . date('dmY', strtotime($fechaActual)) . "_" . date('his', strtotime($fechaActual)) . "_" . $data->id_tipo_documentacion . '.' . $file->getClientOriginalExtension();
                $file->storeAs('archivos', $nombreArchivo, 'public');
                DB::insert("INSERT INTO tb_presupuesto_discapacidad (id_tipo_documentacion,documento,fecha_carga,id_padron) VALUES (?,?,?,?) ", [$data->id_tipo_documentacion, $nombreArchivo, $fechaActual, $data->id_padron]);
            }
            return response()->json(["message" => "El archivo fue registrado correctamente."], 200);
        } catch (\Throwable $th) {
            Storage::delete('public/archivos/' . $nombreArchivo);
            return response()->json(["message" => $th->getMessage()], 500);
        }
    }

    public function srvListarPresupuesto(Request $request)
    {
        $data = [];

        if (!is_null($request->search)) {
            $data = DB::select("SELECT p.id, pd.dni,pd.cuil_benef,concat(pd.apellidos,' ', pd.nombre) as afiliado, p.id,td.descripcion,p.documento,p.fecha_carga
            FROM tb_presupuesto_discapacidad p
            INNER JOIN tb_tipo_documentacion_presupuesto td ON p.id_tipo_documentacion = td.codigo
            INNER JOIN tb_padron pd ON p.id_padron = pd.id WHERE pd.cuil_benef = ?
            ORDER BY p.id DESC", [$request->search]);
        } else {
            $data = DB::select("SELECT p.id, pd.dni,pd.cuil_benef,concat(pd.apellidos,' ', pd.nombre) as afiliado, p.id,td.descripcion,p.documento,p.fecha_carga
            FROM tb_presupuesto_discapacidad p
            INNER JOIN tb_tipo_documentacion_presupuesto td ON p.id_tipo_documentacion = td.codigo
            INNER JOIN tb_padron pd ON p.id_padron = pd.id
            ORDER BY p.id DESC");
        }

        $jsonData = array();
        if (count($data) > 0) {
            foreach ($data as $key) {
                $jsonData[] = array(
                    "id" => $key->id,
                    "dni" => $key->dni,
                    "cuil_benef" => $key->cuil_benef,
                    "afiliado" => $key->afiliado,
                    "descripcion" => $key->descripcion,
                    "documento" => $key->documento,
                    "fecha_carga" => $key->fecha_carga,
                    "url_file" => $key->documento !== '' ? url('/storage/archivos/' . $key->documento) : ''
                );
            }
        }


        return response()->json($jsonData, 200);
    }

    public function srvEliminarPresupuesto(Request $request)
    {

        Storage::delete('public/archivos/' . $request->archivo);
        DB::delete("DELETE FROM tb_presupuesto_discapacidad WHERE id = ? ", [$request->id]);

        return response()->json(["message" => "El registro fue eliminado correctamente."], 200);
    }

    public function srvEliminarPrestacion(Request $request)
    {
        DB::delete("DELETE FROM tb_discapacidad_detalle WHERE id_discapacidad_detalle = ? ", [$request->id_detalle]);
        DB::delete("DELETE FROM tb_discapacidad WHERE id_discapacidad = ? ", [$request->id_discapacidad]);

        return response()->json(["message" => "El registro fue eliminado correctamente."], 200);
    }

    public function srvBuscarPrestacionEdit(Request $request)
    {
        $model = IntegracionDiscapacidadModel::with(['afiliado', 'detalle', 'detalle.practica', 'usuario'])->find($request->id);

        return response()->json($model, 200);
    }

    public function getFiltrarPorUsuario(Request $request)
    {
        $data = DB::select('SELECT d.monto_solicitado,d.num_factura,b.nombre,d.cuil_beneficiario,d.cuil_prestador,d.razon_social_prestador,d.periodo_prestacion,d.cod_usuario,d.monto_comprobante,d.fecha_registra,
                    dt.id_practica FROM tb_discapacidad d INNER JOIN tb_discapacidad_detalle dt ON d.id_discapacidad = dt.id_discapacidad
                     INNER JOIN tb_padron b ON b.cuil_benef = d.cuil_beneficiario WHERE d.cod_usuario = ? AND  d.fecha_registra BETWEEN ? AND ? ', [$request->usuario, $request->desde, $request->hasta]);
        return response()->json($data, 200);
    }
}
