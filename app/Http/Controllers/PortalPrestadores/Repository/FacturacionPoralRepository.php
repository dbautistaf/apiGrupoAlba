<?php

namespace App\Http\Controllers\PortalPrestadores\Repository;

use App\Models\PortalPrestadores\EstadoPortalEntity;
use App\Models\PortalPrestadores\FacturasPortalEntity;
use App\Models\PortalPrestadores\LogComentariosFacturaEntity;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class FacturacionPoralRepository
{
    public function listar()
    {
        return FacturasPortalEntity::with(['tipo', 'estado', 'prestador'])
            ->orderByDesc('id_factura')
            ->get();
    }

    public function obtener($id)
    {
        return FacturasPortalEntity::where('id_factura', $id)
            ->whereNull('fecha_elimina')
            ->first();
    }

    public function crear($data)
    {
        $data['cod_usuario_carga'] = Auth::user()->cod_usuario;
        $data['cod_prestador'] = Auth::user()->id_prestador;
        $data['id_estado'] = 1;

        return FacturasPortalEntity::create($data);
    }

    public function findByExistFactura($params)
    {
        $codPrestador = Auth::user()->id_prestador;
        return FacturasPortalEntity::where('cod_prestador', $codPrestador)
            ->where('num_factura', $params->num_factura)
            ->where('periodo', $params->periodo)
            ->whereNotIn('id_estado', [5]) //NO INCLUIR LAS RECHAZADAS
            ->exists();
    }

    public function actualizar($id, $data)
    {
        $data['cod_usuario_modifica'] = Auth::user()->cod_usuario;
        $factura = FacturasPortalEntity::find($id);
        $data['id_estado'] = $factura->id_estado;
        $data['fecha_carga'] = $factura->fecha_carga;
        $data['cod_usuario_carga'] = $factura->cod_usuario_carga;

        $factura->update($data);

        return $factura;
    }

    public function actualizarEstado($id, $data)
    {
        $data['cod_usuario_modifica'] = Auth::user()->cod_usuario;
        $data['cod_prestador'] = Auth::user()->id_prestador;
        $factura = FacturasPortalEntity::find($id);
        $factura->id_estado = $data->id_estado;
        $factura->observaciones_externas = $data->observaciones_externas;
        $factura->observaciones_internas = $data->observaciones_internas;
        if($data->id_estado == 6){
            $factura->fecha_liquidacion =  Carbon::now();
        }
         if($data->id_estado == 7){
            $factura->fecha_paga =  Carbon::now();
        }
        $factura->update();

        LogComentariosFacturaEntity::create([
            'id_estado' => $data->id_estado,
            'comentario_prestador' => $data->observaciones_externas,
            'comentario_interno' => $data->observaciones_internas,
            'fecha_carga' => Carbon::now(),
            'cod_usuario' => $data->cod_usuario_modifica,
            'id_factura' => $id,
        ]);

        return $factura;
    }

    public function eliminar($id, $usuario)
    {
        $factura = FacturasPortalEntity::find($id);

        $factura->update([
            'fecha_elimina' => Carbon::now(),
            'cod_usuario_modifica' => $usuario
        ]);

        return $factura->delete();
    }

    public function listarEstados()
    {
        $data = EstadoPortalEntity::get();
        return $data;
    }
}
