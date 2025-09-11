<?php

namespace App\Models\configuracion;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TipoGalenosEntity extends Model
{
    use HasFactory;
    protected $table = 'tb_conf_galenos';
    protected $primaryKey = 'id_conf_tipo_galeno';
    public $timestamps = false;

    protected $fillable = [
        'id_tipo_galeno',
        'descripcion',
        'fecha_vigencia',
        'monto_Valor',
        'vigente'
    ];

    public function tipoGaleno()
    {
        return $this->hasOne(TipoPlanGalenosEntity::class, 'id_tipo_galeno', 'id_tipo_galeno');
    }
}
