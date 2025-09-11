<?php

namespace App\Models\convenios;

use App\Models\pratricaMatriz\PracticaMatrizEntity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConvenioDetalleNegociacionContratoEntity extends Model
{
    use HasFactory;
    protected $table = 'tb_convenios_detalle_negociacion';
    protected $primaryKey = 'id_detalle_negociacion';
    public $timestamps = false;

    protected $fillable = [
        'id_negociacion',
        'cantidad',
        'precio_unitario',
        'precio_total',
        'url_adjunto',
        'observaciones',
        'id_identificador_practica',
        'precio_total'
    ];

    public function practica()
    {
        return $this->hasOne(PracticaMatrizEntity::class, 'id_identificador_practica', 'id_identificador_practica');
    }
}
