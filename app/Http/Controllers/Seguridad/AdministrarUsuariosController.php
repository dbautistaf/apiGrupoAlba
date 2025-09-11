<?php

namespace App\Http\Controllers\Seguridad;

use App\Http\Controllers\Seguridad\Repository\UsuarioRepository;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class AdministrarUsuariosController extends Controller
{

    public function getListarUsuarios(UsuarioRepository $repo, Request $request)
    {
        $data = [];
        if (
            $request->estado !== 'Alls'
            && is_null($request->perfil)
            && is_null($request->search)
        ) {
            $data = $repo->findByListAndEstadoAndLimitPage($request->estado, 5);
        } else if (
            $request->estado == 'Alls'
            && is_null($request->perfil)
            && !is_null($request->search)
        ) {
            $data = $repo->findByListAndNombresOrEmailAndLimitPage($request->search, limit: 5);
        } else if (
            $request->estado == 'Alls'
            && is_numeric($request->perfil)
            && is_null($request->search)
        ) {
            $data = $repo->findByListAndPerfilAndLimitPage($request->perfil, 5);
        } else if (
            $request->estado !== 'Alls'
            && is_numeric($request->perfil)
            && is_null($request->search)
        ) {
            $data = $repo->findByListAndPerfilAndEstadoAndLimitPage($request->perfil, $request->estado, 5);
        } else if (
            $request->estado !== 'Alls'
            && is_numeric($request->perfil)
            && !is_null($request->search)
        ) {
            $data = $repo->findByListAndPerfilAndEstadoAndNombresOrEmailAndLimitPage($request->perfil, $request->estado, $request->search, 5);
        } else if (
            $request->estado === 'Alls'
            && is_numeric($request->perfil)
            && !is_null($request->search)
        ) {
            $data = $repo->findByListAndPerfilAndNombresOrEmailAndLimitPage($request->perfil, $request->search, 5);
        } else if (
            $request->estado !== 'Alls'
            && is_null($request->perfil)
            && !is_null($request->search)
        ) {
            $data = $repo->findByListEstadoAndNombresOrEmailAndLimitPage($request->estado, $request->search, 5);
        } else {
            $data = $repo->findByListAndLimitPage($request->page);
        }


        return response()->json($data);
    }

    public function getProcesarUsuario(UsuarioRepository $repo, Request $request)
    {
        if (!is_null($request->cod_usuario)) {
            $repo->findByActualizar($request);
            return response()->json(["message" => "Cuenta de usuario actualizada con éxito"]);
        } else {
            if ($repo->findByValidarCuenta($request->email)) {
                return response()->json(["message" => "La cuenta de correo ya éxiste"], 409);
            }

            if ($request->password !== $request->confirm_password) {
                return response()->json(["message" => "Las contraseñas ingresadas no coinciden"], 409);
            }
            $repo->findByCrear($request);
            return response()->json(["message" => "Cuenta de usuario creada con éxito"]);
        }
    }

    public function getCambiarClaveCuenta(UsuarioRepository $repo, Request $request)
    {
        if (!is_null($request->email)) {
            $repo->findByCambiarClaveCuenta($request);
        } else {
            return response()->json(["message" => "Ingrese una cuenta de email valido"], 409);
        }
        return response()->json(["message" => "La contraseña de la cuenta <b>" . $request->email . "</b> fue cambiada con éxito"]);
    }

    public function getHabilitarCuenta(UsuarioRepository $repo, Request $request)
    {
        if (!is_null($request->email)) {
            $repo->findByHabilitarCuenta($request);
        } else {
            return response()->json(["message" => "Ingrese una cuenta de email valido"], 409);
        }
        return response()->json(["message" => "La cuenta del usuario <b>" . $request->email . "</b> fue habilitado con éxito"]);
    }

    public function getDeshabilitarCuenta(UsuarioRepository $repo, Request $request)
    {
        if (!is_null($request->email)) {
            $repo->findByDeshabilitarCuenta($request);
        } else {
            return response()->json(["message" => "Ingrese una cuenta de email valido"], 409);
        }
        return response()->json(["message" => "La cuenta del usuario <b>" . $request->email . "</b> fue deshabilitado con éxito"]);
    }
}
