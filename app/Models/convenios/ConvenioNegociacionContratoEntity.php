<?php

namespace App\Models\convenios;

use App\Models\prestadores\PrestadorEntity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConvenioNegociacionContratoEntity extends Model
{
    use HasFactory;
    protected $table = 'tb_convenios_negociacion_contratos';
    protected $primaryKey = 'id_negociacion';
    public $timestamps = false;

    protected $fillable = [
        'cod_prestador',
        'id_tipo_propuesta',
        'id_usuario',
        'id_sector',
        'fecha_inicio',
        'fecha_fin',
        'estado',
        'valor',
        'observaciones',
        'cod_convenio'
    ];

    public function prestador()
    {
        return $this->hasOne(PrestadorEntity::class, 'cod_prestador', 'cod_prestador');
    }

    public function tipoPropuesta()
    {
        return $this->hasOne(ConvenioTipoPropuestaEntity::class, 'id_tipo_propuesta', 'id_tipo_propuesta');
    }

    public function tipoSector()
    {
        return $this->hasOne(ConvenioTipoSectoresEntity::class, 'id_sector', 'id_sector');
    }
}
