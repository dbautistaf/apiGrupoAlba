<?php

namespace App\Http\Controllers\Emails;

use App\Http\Controllers\PrestacionesMedicas\Repository\PrestacionMedicaRepository;
use App\Mail\PrestacionMedicaMail;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Mail;

class PrestacionMedicaController extends Controller
{

    public function getEnviarMail(Request $request, PrestacionMedicaRepository  $prestacionRepository)
    {
        $data = new \stdClass();
        $data->asunto = $request->asunto;
        $data->afiliado = $request->afiliado;
        $data->estado = $request->estado;
        $data->hospital = $request->hospital;
        $prestacion = $prestacionRepository->findByIdPrestacion($request->cod_prestacion);
        $data->detalle = $prestacion->detalle;
        $data->numero_tramite = $prestacion->numero_tramite;
        $data->numero_tramite = $prestacion->numero_tramite;
        $data->fecha_autorizacion = $prestacion->fecha_autorizacion;

        Mail::to($request->email)->send(new PrestacionMedicaMail($data));

        return response()->json([
            'success' => true,
            'message' => 'Email enviado correctamente'
        ]);
    }
}
