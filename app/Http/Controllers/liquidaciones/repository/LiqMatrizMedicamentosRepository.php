<?php

namespace App\Http\Controllers\liquidaciones\repository;

use App\Models\liquidaciones\LiqMatrizMedicamentosEntity;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class LiqMatrizMedicamentosRepository
{
    public function findBySave($params)
    {
        $user = Auth::user();
        $fechaActual = Carbon::now('America/Argentina/Buenos_Aires');
        return LiqMatrizMedicamentosEntity::create([
            'troquel' => $params->troquel,
            'registro' => $params->registro,
            'nombre' => $params->nombre,
            'presentacion' => $params->presentacion,
            'laboratorio' => $params->laboratorio,
            'droga' => $params->droga,
            'accion' => $params->accion,
            'acargo_ospf' => $params->acargo_ospf,
            'autorizacion_previa' => $params->autorizacion_previa,
            'activo' => '1',
            'fecha_registra' => $fechaActual,
            'cod_usuario' => $user->cod_usuario,
            'precio_venta' => $params->precio_venta,
            'precio_compra' => $params->precio_compra,
            'tipo_venta' => $params->tipo_venta,
        ]);
    }

    public function findByUpdate($params)
    {
        $med = LiqMatrizMedicamentosEntity::find($params->id_medicamento);

        $med->troquel = $params->troquel;
        $med->registro = $params->registro;
        $med->nombre = $params->nombre;
        $med->presentacion = $params->presentacion;
        $med->laboratorio = $params->laboratorio;
        $med->droga = $params->droga;
        $med->accion = $params->accion;
        $med->acargo_ospf = $params->acargo_ospf;
        $med->autorizacion_previa = $params->autorizacion_previa;
        $med->precio_venta = $params->precio_venta;
        $med->precio_compra = $params->precio_compra;
        $med->tipo_venta = $params->tipo_venta;
        $med->update();
        return $med;
    }

    public function findbyListAlls($rows)
    {
        return LiqMatrizMedicamentosEntity::limit($rows)
            ->get();
    }

    public function findbyListTroquel($troquel, $rows)
    {
        return LiqMatrizMedicamentosEntity::where('troquel', $troquel)
            ->limit($rows)
            ->get();
    }

    public function findbyListMedicamento($search, $rows)
    {
        return LiqMatrizMedicamentosEntity::where('nombre', 'LIKE', '%' . $search . '%')
            ->orWhere('droga', 'LIKE', '%' . $search . '%')
            ->limit($rows)
            ->get();
    }

    public function deleteId($id)
    {
        return LiqMatrizMedicamentosEntity::find($id)->delete();
    }
}
