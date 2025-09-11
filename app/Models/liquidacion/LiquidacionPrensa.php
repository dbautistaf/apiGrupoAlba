<?php

namespace App\Models\liquidacion;

use App\Models\afiliado\AfiliadoPadronEntity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LiquidacionPrensa extends Model
{
    use HasFactory;
    protected $table = 'tb_liquidaciones_prensa';
    protected $primaryKey = 'id_prensa';
    public $timestamps = false;

    protected $fillable = [
        'id',
        'organ',
        'codconc',
        'importe',
        'fecproc',
        'fecrec',
        'cuitcont',
        'periodo',
        'idtranfer',
        'cuitapo',
        'banco',
        'codsuc',
        'zona',
        'gerenciador',
        'afiliado_nombre',
        'afiliado_apellido',
        'activo',
        'razonsocial',
        'obra_social',
    ];

    public function PadronAfil()
    {
        return $this->hasOne(AfiliadoPadronEntity::class, 'cuil_benef', 'cuitapo');
    }
}
