<?php

namespace App\Http\Controllers\Auth\Services;

use App\Http\Controllers\Auth\Repository\RecuperarContraseniaRepository;
use App\Mail\RecuperarContraseniaMail;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;

class RecuperarContraseniaController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['getVerificarCuenta']]);
    }

    public function getVerificarCuenta(Request $request, RecuperarContraseniaRepository $repo)
    {
        $user = $repo->findByValidarCuenta($request->email);
        $URL_FRONT = env('APP_URL_CLIENT');

        if ($user == null) {
            return response()->json(['message' => "La cuenta de correo <b>$request->email</b> no existe. Verifique la dirección e intente nuevamente."], 409);
        }

        if ($user->estado_cuenta == '0') {
            return response()->json(['message' => "La cuenta de correo <b>$request->email</b> está bloqueada. Por favor, contacte al soporte para más información."], 409);
        }

        $userVerificado = $repo->findByGnerarCodigoVerificador($user);

        $evento = $repo->findByRegistrarEvento($userVerificado, '', '', '');

        $link = "$URL_FRONT/#/auth/validar-cambio-contrasenia/$evento->jwt";

        Mail::to($userVerificado->email)->send(new RecuperarContraseniaMail($userVerificado, $link));

        return response()->json(['message' => "Hemos enviado un enlace a su correo electrónico <b>$request->email</b> para restablecer su contraseña. Por favor, revise su bandeja de entrada y siga las instrucciones."]);
    }

    public function getCambiarClave(Request $request, RecuperarContraseniaRepository $repo)
    {
        DB::beginTransaction();
        try {
            DB::commit();
            $user = $repo->findByValidarCuenta($request->email);

            if ($user == null) {
                return response()->json(['message' => "La cuenta de correo <b>$request->email</b> no existe. Verifique la dirección e intente nuevamente."], 409);
            }

            if ($repo->findByValidarDigitoVerificador($request->digito)) {
                $userModificado =  $repo->findByUpdateContrasenia($user, $request->clave);

                $repo->findByFinalizarEvento($request->digito);

                JWTAuth::invalidate(JWTAuth::getToken());

                $jwt = $repo->findByIsTokenLogin($userModificado);

                return response()->json(["message" => "La contraseña de la cuenta de correo <b>$request->email</b> fue actualizada con éxito.", "jwt" => $jwt]);
            } else {
                DB::rollBack();
                return response()->json([
                    'message' => 'Te crees habil intentado vulverar nuestra plataforma. Atte. CYGNUS - OSPF'
                ], 409);
            }
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'message' => $th->getMessage()
            ], 500);
        }
    }
}
