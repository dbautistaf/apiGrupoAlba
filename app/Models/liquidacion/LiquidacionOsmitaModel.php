<?php

namespace App\Models\liquidacion;

use App\Models\afiliado\AfiliadoPadronEntity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LiquidacionOsmitaModel extends Model
{
    use HasFactory;
    protected $table = 'tb_liquidaciones_osmita';
    //protected $primaryKey = 'id_osmita';
    public $timestamps = false;

    protected $fillable = [
        'tipoaf',
        'cuit',
        'razonsoc',
        'cuil',
        'nomyape',
        'nroaf',
        'codaf',
        'sistmed',
        'nroasoc',
        'capitas',
        'fec_alta',
        'periodo',
        'rence',
        'remap',
        'djtotce',
        'djtotap',
        'apoce',
        'apoap',
        'apoyco',
        'a_pagar',
        'fec_rec',
        'codconc',
        'OBRA_SOCIAL',
    ];
    
    public function PadronAfil()
    {
        return $this->hasOne(AfiliadoPadronEntity::class, 'cuil_benef', 'cuil');
    }
}
