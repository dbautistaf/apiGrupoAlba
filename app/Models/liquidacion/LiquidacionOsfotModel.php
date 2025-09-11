<?php

namespace App\Models\liquidacion;

use App\Models\afiliado\AfiliadoPadronEntity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LiquidacionOsfotModel extends Model
{
    use HasFactory;
    protected $table = 'tb_liquidaciones_osfot';
    //protected $primaryKey = 'id_osfot';
    public $timestamps = false;

    protected $fillable = [
        'CONVENIO',
        'FILIAL',
        'CUIT',
        'EMPRESA',
        'PERIODO',
        'CUIL',
        'NOMBRE',
        'REMUNERA',
        'APORTE',
        'CONTRI',
        'MONO',
        'OTROS',
        'TOTAL',
        'OBRA_SOCIAL',
    ];

    public function PadronAfil()
    {
        return $this->hasOne(AfiliadoPadronEntity::class, 'cuil_benef', 'cuil');
    }
}
