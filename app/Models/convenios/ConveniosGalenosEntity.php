<?php

namespace App\Models\convenios;

use App\Models\configuracion\TipoGalenosEntity;
use App\Models\configuracion\TipoPlanGalenosEntity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConveniosGalenosEntity extends Model
{
    use HasFactory;
    protected $table = 'tb_convenios_gelenos';
    protected $primaryKey = 'id_convenio_galeno';
    public $timestamps = false;

    protected $fillable = [
        'cod_convenio',
        'id_conf_tipo_galeno',
        'tipo_importe',
        'monto_anterior_valor_base',
        'monto_valor_base',
        'monto_valor_convenio',
        'tipo_valor_base'
    ];

    public function tipoGaleno()
    {
        return $this->hasOne(TipoGalenosEntity::class, 'id_conf_tipo_galeno', 'id_conf_tipo_galeno');
    }
}
