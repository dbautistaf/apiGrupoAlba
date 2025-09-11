<?php

namespace App\Models\tratamientoProlongado;

use App\Models\vademecumModelo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class detalleTratamientoModel extends Model
{
    use HasFactory;
    protected $table = 'tb_tratamiento_detalle';
    protected $primaryKey = 'id_detalle';
    public $timestamps = false;

    protected $fillable = [
        'id_vademecum',
        'nombre_comercial',
        'dosis',
        'envases_mensuales',
        'id_tratamiento',
    ];

    public function tratamiento()
    {
        return $this->belongsTo(tratamientoProlongadoModel::class, 'id_tratamiento', 'id_tratamiento');
    }

    public function vademecum()
    {
        return $this->hasOne(vademecumModelo::class, 'id_vademecum', 'id_vademecum');
    }
}
