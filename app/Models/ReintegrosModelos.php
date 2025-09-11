<?php

namespace App\Models;

use App\Models\afiliado\AfiliadoPadronEntity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReintegrosModelos extends Model
{
    use HasFactory;
    protected $table = 'tb_reintegros';
    protected $primaryKey = 'nro_reintegro';
    public $timestamps = false;

    protected $fillable = [
        'fecha_solicitud',
        'fecha_transferencia',
        'url_adjunto',
        'motivo',
        'importe_solicitado',
        'importe_reconocido',
        'autorizado_por',
        'observaciones',
        'cbu_prestador',
        'nro_factura',
        'id_usuario',
        'id_filial',
        'id_afiliados',
        'id_estado_autorizacion',
        'fecha_carga',
        'nombre_prestador',
        'estado',
        'cantidad',
        'observaciones_auditoria'
    ];

    public function Afiliado()
    {
        return $this->hasOne(AfiliadoPadronEntity::class, 'id','id_afiliados');
    }

    public function Autorizacion()
    {
        return $this->hasOne(EstadoAutorizacionModelos::class,'id_estado_autorizacion', 'id_estado_autorizacion');
    }

    public function Filial()
    {
        return $this->hasOne(FilialModelos::class,'id_filial', 'id_filial');
    }
}
