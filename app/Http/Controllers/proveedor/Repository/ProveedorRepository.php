<?php

namespace App\Http\Controllers\proveedor\Repository;

use App\Models\proveedor\DatosBancariosEntity;
use App\Models\proveedor\MatrizProveedoresEntity;
use App\Models\proveedor\MetodoPagoProveedorEntity;
use Illuminate\Support\Facades\Auth;

class ProveedorRepository
{

    public function findBySaveFlash($params)
    {
        $user = Auth::user();
        return MatrizProveedoresEntity::create([
            'cuit' => $params->cuit,
            'razon_social' => $params->razon_social,
            'nombre_fantasia' => $params->nombre_fantasia,
            'fecha_alta' => $params->fecha_alta,
            'celular' => $params->celular,
            'codigo_postal_telefono' => $params->codigo_postal_telefono,
            'email' => $params->email,
            'direccion' => $params->direccion,
            'cod_tipo_iva' => $params->cod_tipo_iva,
            'id_regimen' => $params->id_regimen,
            'cod_tipo_impuesto' => $params->cod_tipo_impuesto,
            'cod_usuario' => $user->cod_usuario,
            'id_proveedor_tipo' => $params->id_proveedor_tipo,
            'vigente' => '1'
        ]);
    }

    public function findBySave($params)
    {
        $user = Auth::user();
        return MatrizProveedoresEntity::create([
            'cuit' => $params->cuit,
            'razon_social' => $params->razon_social,
            'nombre_fantasia' => $params->nombre_fantasia,
            'fecha_alta' => $params->fecha_alta,
            'fecha_baja' => $params->fecha_baja,
            'celular' => $params->celular,
            'codigo_postal_telefono' => $params->codigo_postal_telefono,
            'email' => $params->email,
            'direccion' => $params->direccion,
            'observaciones' => $params->observaciones,
            'cod_tipo_impuesto' => $params->cod_tipo_impuesto,
            'cod_tipo_iva' => $params->cod_tipo_iva,
            'departamento' => $params->departamento,
            'cod_localidad' => $params->cod_localidad,
            'cod_usuario' => $user->cod_usuario,
            'vigente' => $params->vigente,
            'id_regimen' => $params->id_regimen,
            'id_proveedor_tipo' => $params->id_proveedor_tipo
        ]);
    }

    public function findBySaveDatosBancarios($params, $cod_proveedor)
    {
        if (
            !is_null($params['numero_cuenta'])
            && !is_null($params['titular_cuenta'])
            && !is_null($params['tipo_cuenta'])
            && !is_null($params['cbu_cuenta'])
        ) {
            return DatosBancariosEntity::create([
                'numero_cuenta' => $params['numero_cuenta'],
                'titular_cuenta' => $params['titular_cuenta'],
                'tipo_cuenta' => $params['tipo_cuenta'],
                'cbu_cuenta' => $params['cbu_cuenta'],
                'vigente' => $params['vigente'],
                'cod_proveedor' => $cod_proveedor
            ]);
        }
    }

    public function findByMetodoPago($params, $cod_proveedor)
    {
        if (
            !is_null($params['id_tipo_metodo_pago'])
            && !is_null($params['dia_corte_mensual'])
            && !is_null($params['dia_pago_antes_vencimiento'])
            && !is_null($params['dia_pago_despues_vencimiento'])
        ) {
            return MetodoPagoProveedorEntity::create([
                'cod_proveedor' => $cod_proveedor,
                'id_tipo_metodo_pago' => $params['id_tipo_metodo_pago'],
                'dia_corte_mensual' => $params['dia_corte_mensual'],
                'dia_pago_antes_vencimiento' => $params['dia_pago_antes_vencimiento'],
                'dia_pago_despues_vencimiento' => $params['dia_pago_despues_vencimiento']
            ]);
        }
    }

    public function findByUpdateMetodoPago($params, $cod_proveedor)
    {
        $pago = MetodoPagoProveedorEntity::find($params['id_pago_proveedor']);
        $pago->cod_proveedor = $cod_proveedor;
        $pago->id_tipo_metodo_pago = $params['id_tipo_metodo_pago'];
        $pago->dia_corte_mensual = $params['dia_corte_mensual'];
        $pago->dia_pago_antes_vencimiento = $params['dia_pago_antes_vencimiento'];
        $pago->dia_pago_despues_vencimiento = $params['dia_pago_despues_vencimiento'];
        $pago->update();
    }

    public function findByUpdateProveedor($params)
    {
        $proveedor = MatrizProveedoresEntity::find($params->cod_proveedor);
        $proveedor->cuit = $params->cuit;
        $proveedor->razon_social = $params->razon_social;
        $proveedor->nombre_fantasia = $params->nombre_fantasia;
        $proveedor->fecha_alta = $params->fecha_alta;
        $proveedor->fecha_baja = $params->fecha_baja;
        $proveedor->celular = $params->celular;
        $proveedor->codigo_postal_telefono = $params->codigo_postal_telefono;
        $proveedor->email = $params->email;
        $proveedor->direccion = $params->direccion;
        $proveedor->observaciones = $params->observaciones;
        $proveedor->cod_tipo_impuesto = $params->cod_tipo_impuesto;
        $proveedor->cod_tipo_iva = $params->cod_tipo_iva;
        $proveedor->departamento = $params->departamento;
        $proveedor->cod_localidad = $params->cod_localidad;
        $proveedor->vigente = $params->vigente;
        $proveedor->id_regimen = $params->id_regimen;
        $proveedor->id_proveedor_tipo = $params->id_proveedor_tipo;
        $proveedor->update();
    }

    public function findByUpdateDatosBancarios($params)
    {
        $banco = DatosBancariosEntity::find($params['cod_dato_bancario']);
        $banco->numero_cuenta = $params['numero_cuenta'];
        $banco->titular_cuenta = $params['titular_cuenta'];
        $banco->tipo_cuenta = $params['tipo_cuenta'];
        $banco->cbu_cuenta = $params['cbu_cuenta'];
        $banco->update();
    }

    public function findByExisteCuit($cuit)
    {
        return MatrizProveedoresEntity::where('cuit', $cuit)->exists();
    }
}
