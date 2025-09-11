<?php

namespace App\Models\convenios;

use App\Models\prestadores\PrestadorEntity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConveniosPrestadoresEntity extends Model
{
    use HasFactory;

    protected $table = 'tb_convenios_prestador';
    protected $primaryKey = 'cod_convenio_prestador';
    public $timestamps = false;

    protected $fillable = [
        'cod_prestador',
        'estado',
        'cod_convenio',
        'id_tipo_comprobantes',
        'iva_id_alicuota_iva',
        'id_tipo_valor_pago',
        'forma_pago'
    ];

    public function prestador()
    {
        return $this->hasOne(PrestadorEntity::class, 'cod_prestador', 'cod_prestador');
    }

    public function tipoComprobante()
    {
        return $this->hasOne(ConvenioTipoComprobanteEntity::class, 'id_tipo_comprobantes', 'id_tipo_comprobantes');
    }

    public function alicuotaIva()
    {
        return $this->hasOne(ConveniosAlicuotaIvaEntity::class, 'id_alicuota_iva', 'iva_id_alicuota_iva');
    }

    public function medioPago()
    {
        return $this->hasOne(ConveniosTipoMedioPagoEntity::class, 'id_tipo_valor_pago', 'id_tipo_valor_pago');
    }

    public function datosBancarios()
    {
        return $this->hasOne(ConveniosDatosBancariosPrestadorEntity::class, 'cod_convenio_prestador', 'cod_convenio_prestador');
    }
}
