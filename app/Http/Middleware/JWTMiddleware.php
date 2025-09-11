<?php

namespace App\Http\Middleware;
use Closure;
use Illuminate\Support\Facades\Auth;
use JWTAuth;
use Exception;
use PHPOpenSourceSaver\JWTAuth\Exceptions\JWTException;
use PHPOpenSourceSaver\JWTAuth\Exceptions\TokenExpiredException;
use PHPOpenSourceSaver\JWTAuth\Exceptions\TokenInvalidException;

class JWTMiddleware{
    public function handle($request, Closure $next)
    {
        try {
            $user = \PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth::parseToken()->authenticate();
        } catch (Exception  $e) {
            if ($e instanceof TokenInvalidException){
                return response()->json(['status' => 401,'message' => 'Lo sentimos su sessión ha expirado, por su seguridad vuelva a ingresar.','expired' => true],401);
            }else if ($e instanceof TokenExpiredException){
                return response()->json(['status' => 401,'message' => 'Lo sentimos su sessión ha expirado.', 'expired' => true],401);
            }else{
                return response()->json(['status' => 401,'message' => 'Estimado usuario necesitas un token de acceso. E02054', 'expired' => false],401);
            }
        }

       /*  if (!$user) {
            return response()->json(['error' => 'Lo sentimos se solicita un token de validación.'], 401);
        } */

        // Autenticar al usuario
        Auth::login($user);

        return $next($request);
    }
}
