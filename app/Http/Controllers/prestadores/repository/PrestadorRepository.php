<?php

namespace App\Http\Controllers\prestadores\repository;

use App\Models\prestadores\DatosBancariosPrestadorEntity;
use App\Models\prestadores\MetodoPagoPrestadorEntity;
use App\Models\prestadores\PrestadorEntity;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class PrestadorRepository
{

    private $user;
    private $fechaActual;

    public function __construct()
    {
        $this->user = Auth::user();
        $this->fechaActual = Carbon::now('America/Argentina/Buenos_Aires');
    }

    public function findBySaveFlash($params)
    {

        return PrestadorEntity::create([
            'cuit' => $params->cuit,
            'razon_social' => $params->razon_social,
            'nombre_fantasia' => $params->nombre_fantasia,
            'fecha_alta' => $params->fecha_alta,
            'celular' => $params->celular,
            'codigo_postal_telefono' => $params->codigo_postal_telefono,
            'email' => $params->email,
            'direccion' => $params->direccion,
            'cod_tipo_prestador' => $params->cod_tipo_prestador,
            'cod_tipo_impuesto' => $params->cod_tipo_impuesto,
            'cod_tipo_iva' => $params->cod_tipo_iva,
            'cod_usuario' => $this->user->cod_usuario,
            'vigente' => '1'
        ]);
    }

    public function findByCrearMetodoPago($params, $cod_prestador)
    {
        if (
            !is_null($params['id_tipo_metodo_pago'])
            && !is_null($params['dia_corte_mensual'])
            && !is_null($params['dia_pago_antes_vencimiento'])
            && !is_null($params['dia_pago_despues_vencimiento'])
        ) {
            return MetodoPagoPrestadorEntity::create([
                'cod_prestador' => $cod_prestador,
                'id_tipo_metodo_pago' => $params['id_tipo_metodo_pago'],
                'dia_corte_mensual' => $params['dia_corte_mensual'],
                'dia_pago_antes_vencimiento' => $params['dia_pago_antes_vencimiento'],
                'dia_pago_despues_vencimiento' => $params['dia_pago_despues_vencimiento']
            ]);
        }

    }

    public function findByUpdateMetodoPago($params, $cod_prestador)
    {
        $pago = MetodoPagoPrestadorEntity::find($params['id_pago_proveedor']);
        $pago->cod_prestador = $cod_prestador;
        $pago->id_tipo_metodo_pago = $params['id_tipo_metodo_pago'];
        $pago->dia_corte_mensual = $params['dia_corte_mensual'];
        $pago->dia_pago_antes_vencimiento = $params['dia_pago_antes_vencimiento'];
        $pago->dia_pago_despues_vencimiento = $params['dia_pago_despues_vencimiento'];
        $pago->update();
    }


    public function findByCrearDatosBancarios($params, $cod_prestador, $cuit)
    {
        if (
            !is_null($params["numero_cuenta"]) && !is_null($params["titular_cuenta"])
            && !is_null($params["tipo_cuenta"])
            && !is_null($params["cbu_cuenta"])
        ) {
            return DatosBancariosPrestadorEntity::create([
                'numero_cuenta' => $params["numero_cuenta"],
                'titular_cuenta' => $params["titular_cuenta"],
                'tipo_cuenta' => $params["tipo_cuenta"],
                'cbu_cuenta' => $params["cbu_cuenta"],
                'cbu_cuenta1' => $params["cbu_cuenta1"],
                'cbu_cuenta2' => $params["cbu_cuenta2"],
                'vigente' => $params["vigente"],
                'cod_prestador' => $cod_prestador,
                'cuit_prestador' => $cuit
            ]);
        }


    }

    public function findByUpdateDatosBancarios($params, $cod_prestador, $cuit)
    {
        $datosBancarios = DatosBancariosPrestadorEntity::find($params["cod_banco_empresa"]);
        $datosBancarios->numero_cuenta = $params["numero_cuenta"];
        $datosBancarios->titular_cuenta = $params["titular_cuenta"];
        $datosBancarios->tipo_cuenta = $params["tipo_cuenta"];
        $datosBancarios->cbu_cuenta = $params["cbu_cuenta"];
        $datosBancarios->cbu_cuenta1 = $params["cbu_cuenta1"];
        $datosBancarios->cbu_cuenta2 = $params["cbu_cuenta2"];
        $datosBancarios->vigente = $params["vigente"];
        $datosBancarios->cod_prestador = $cod_prestador;
        $datosBancarios->cuit_prestador = $cuit;
        return $datosBancarios->update();
    }

    public function findByUpdatePrestador($params)
    {
        $prestador = PrestadorEntity::find($params->cod_prestador);
        $prestador->cuit = $params->cuit;
        $prestador->razon_social = $params->razon_social;
        $prestador->nombre_fantasia = $params->nombre_fantasia;
        $prestador->fecha_alta = $params->fecha_alta;
        $prestador->fecha_baja = $params->fecha_baja;
        $prestador->numero_inscripcion_super = $params->numero_inscripcion_super;
        $prestador->celular = $params->celular;
        $prestador->codigo_postal_telefono = $params->codigo_postal_telefono;
        $prestador->email = $params->email;
        $prestador->email1 = $params->email1;
        $prestador->email2 = $params->email2;
        $prestador->direccion = $params->direccion;
        $prestador->observaciones = $params->observaciones;
        $prestador->cod_tipo_prestador = $params->cod_tipo_prestador;
        $prestador->cod_tipo_impuesto = $params->cod_tipo_impuesto;
        $prestador->cod_tipo_iva = $params->cod_tipo_iva;
        $prestador->departamento = $params->departamento;
        $prestador->cod_localidad = $params->cod_localidad;
        $prestador->vigente = $params->vigente;
        $prestador->id_regimen = $params->id_regimen;
        $prestador->id_tipo_efector = $params->id_tipo_efector;
        $prestador->update();
        return $prestador;
    }

    public function findByExistCuit($cuit)
    {
        return PrestadorEntity::where('cuit', $cuit)->exists();
    }

    public function findByCrearPrestador($params)
    {
        return PrestadorEntity::create([
            'cuit' => $params->cuit,
            'razon_social' => $params->razon_social,
            'nombre_fantasia' => $params->nombre_fantasia,
            'fecha_alta' => $params->fecha_alta,
            'fecha_baja' => $params->fecha_baja,
            'numero_inscripcion_super' => $params->numero_inscripcion_super,
            'celular' => $params->celular,
            'codigo_postal_telefono' => $params->codigo_postal_telefono,
            'email' => $params->email,
            'email1' => $params->email1,
            'email2' => $params->email2,
            'direccion' => $params->direccion,
            'observaciones' => $params->observaciones,
            'cod_tipo_prestador' => $params->cod_tipo_prestador,
            'cod_tipo_impuesto' => $params->cod_tipo_impuesto,
            'cod_tipo_iva' => $params->cod_tipo_iva,
            'departamento' => $params->departamento,
            'cod_localidad' => $params->cod_localidad,
            'cod_usuario' => $this->user->cod_usuario,
            'vigente' => $params->vigente,
            'id_regimen' => $params->id_regimen,
            'id_tipo_efector' => $params->id_tipo_efector,
        ]);
    }

    public function findById($id)
    {
        return PrestadorEntity::with([
            "tipoPrestador",
            "tipoImpuesto",
            "tipoIva",
            "localidad",
            "datosBancarios",
            "metodoPago",
            "tiposImputaciones",
            "tiposImputaciones.imputacion"
        ])->find($id);
    }


    public function findByMetodoPago($codPrestador)
    {
        return MetodoPagoPrestadorEntity::where('cod_prestador', $codPrestador)->first();
    }
}
