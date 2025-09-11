<?php

namespace App\Models\Afip;

use App\Models\afiliado\AfiliadoPadronEntity;
use App\Models\EmpresaModelo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeclaracionesJuradasModelo extends Model
{
    use HasFactory;
    protected $table = 'tb_declaraciones_juradas';
    protected $primaryKey = 'id_ddjj';
    public $timestamps = false;

    protected $fillable = [
        'codosoc',
        'periodo',
        'cuit',
        'cuil',
        'remimpo',
        'imposad',
        'zona',
        'grpfam',
        'nogrpfam',
        'secoblig',
        'condicion',
        'situacion',
        'actividad',
        'modalidad',
        'ceros_demas',
        'codsini',
        'apadios',
        'version',
        'rem5',
        'esposa',
        'excosapo',
        'indret',
        'indexccon',
        'fecpresent',
        'fecproc',
        'origrect',
        'apobsoc',
        'conos',
        'remtot',
        'codosoc_inform',
        'rembase_cos',
        'release_ver',
        'periodo_ddjj',
        'fecha_proceso',
        'id_usuario',
    ];
    public function PadronAfil()
    {
        return $this->hasOne(AfiliadoPadronEntity::class, 'cuil_tit', 'cuil');
    }

    public function Empresa()
    {
        return $this->hasOne(EmpresaModelo::class, 'cuit', 'cuit');
    }
}
