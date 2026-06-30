<?php

namespace App\Models\PortalPrestadores;

use App\Models\facturacion\TipoFacturacionEntity;
use App\Models\prestadores\PrestadorEntity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FacturasPortalEntity extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $table = 'tb_portal_prestador_facturacion';
    protected $primaryKey = 'id_factura';
    public $timestamps = true;
    const CREATED_AT = 'fecha_carga';
    const UPDATED_AT = 'fecha_modifica';
    const DELETED_AT = 'fecha_elimina';

    protected $fillable = [
        'cod_prestador',
        'id_tipo_factura',
        'num_factura',
        'periodo',
        'observaciones',
        'id_estado',
        'fecha_carga',
        'cod_usuario_carga',
        'fecha_modifica',
        'cod_usuario_modifica',
        'fecha_elimina',
        'observaciones_externas',
        'observaciones_internas',
        'fecha_paga',
        'fecha_liquidacion'
    ];

    public function tipo()
    {
        return $this->hasOne(TipoFacturacionEntity::class, 'id_tipo_factura', 'id_tipo_factura');
    }

    public function estado()
    {
        return $this->hasOne(EstadoPortalEntity::class, 'id_estado', 'id_estado');
    }

    public function prestador()
    {
        return $this->hasOne(PrestadorEntity::class, 'cod_prestador', 'cod_prestador');
    }
}
