<?php

namespace App\Models\liquidaciones;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LiqTipoMotivoDebitoEntity extends Model
{
    use HasFactory;

    protected $table = 'tb_liquidaciones_tipo_motivos_debito';
    protected $primaryKey = 'id_tipo_motivo_debito';
    public $timestamps = false;

    protected $fillable = [
        'descripcion_motivo',
        'patalogia_cie',
        'vigente'
    ];
}
