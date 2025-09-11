<?php

namespace App\Models\liquidacion;

use App\Models\afiliado\AfiliadoPadronEntity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LiquidacionOsceara extends Model
{
    use HasFactory;
    protected $table = 'tb_liquidaciones_osceara';
    protected $primaryKey = 'id_osceara';
    public $timestamps = false;

    protected $fillable = [
        'apellido_nombre',
        'nombre',
        'cuil',
        'cuit',
        'nro_afiliado',
        'periodo',
        'empresa',
        'remun_rem',
        'remdj_ct',
        'remdj_st',
        'apo_trf',
        'con_trf',
        'tot_trf',
        'impdj',
        'obra_social',
    ];

    public function PadronAfil()
    {
        return $this->hasOne(AfiliadoPadronEntity::class, 'cuil_benef', 'cuil');
    }
}
