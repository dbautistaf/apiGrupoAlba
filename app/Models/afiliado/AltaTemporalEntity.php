<?php

namespace App\Models\afiliado;

use App\Models\DetalleTipoDocAfiliadoModelo;
use App\Models\LocatorioModelos;
use App\Models\SexoModelo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AltaTemporalEntity extends Model
{
    use HasFactory;
    protected $table = 'tb_tipo_plan';
    protected $primaryKey = 'id_tipo_plan';
    public $timestamps = false;

    protected $fillable = [
        'cuil_tit',
        'cuil_benef',
        'id_tipo_documento',
        'dni',
        'nombre',
        'apellidos',
        'id_sexo',
        'fe_nac',
        'fe_alta',
        'id_usuario',
        'id_tipo_beneficiario',
        'id_parentesco',
        'fe_baja',
        'activo',
        'observaciones',
        'id_locatario',
    ];

    public function documentos()
    {
        return $this->hasMany(DetalleTipoDocAfiliadoModelo::class, 'id_padron', 'id');
    }

    public function sexo()
    {
        return $this->hasOne(SexoModelo::class, 'id_sexo', 'id_sexo');
    }

    public function obrasocial()
    {
        return $this->hasOne(LocatorioModelos::class,  'id_locatorio', 'id_locatario');
    }

    public function tipoParentesco()
    {
        return $this->hasOne(AfiliadoTipoParentescoEntity::class, 'id_parentesco', 'id_parentesco');
    }
}
