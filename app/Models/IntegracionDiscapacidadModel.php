<?php

namespace App\Models;

use App\Models\afiliado\AfiliadoPadronEntity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IntegracionDiscapacidadModel extends Model
{
    use HasFactory;
    protected $table = 'tb_discapacidad';
    protected $primaryKey = 'id_discapacidad';
    public $timestamps = false;

    protected $fillable = [
        'cuil_beneficiario',
        'codigo_certificado',
        'vnto_certificado',
        'periodo_prestacion',
        'cuil_prestador',
        'id_tipo_comprobante',
        'id_tipo_emision',
        'fecha_emision_comprobante',
        'num_cae_cai',
        'punto_venta',
        'num_comprobante',
        'monto_comprobante',
        'monto_solicitado',
        'dependencia',
        'cod_usuario',
        'fecha_registra',
        'id_provincia_discapacidad',
        'num_factura',
        'codigo',
        'modulo',
        'razon_social_prestador',
        'categoria',
        'tipo_archivo',
        'procesado',
        'numero_liquidacion'
    ];

    public function afiliado()
    {
        return $this->hasOne(AfiliadoPadronEntity::class, 'cuil_benef', 'cuil_beneficiario');
    }

    public function detalle()
    {
        return $this->hasMany(DiscaPacidadDetalleModel::class, 'id_discapacidad', 'id_discapacidad');
    }

    public function prestador(){
        return $this->hasOne(PrestadoresDiscaModel::class,'cuit','cuil_prestador');
    }

    public function usuario(){
        return $this->hasOne(User::class,'cod_usuario','cod_usuario');
    }
}
