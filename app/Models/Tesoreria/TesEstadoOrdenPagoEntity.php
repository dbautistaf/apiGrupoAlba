<?php

namespace App\Models\Tesoreria;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TesEstadoOrdenPagoEntity extends Model
{
    use HasFactory;

    protected $table = 'tb_tes_estado_orden_pago';
    protected $primaryKey = 'id_estado_orden_pago';
    public $timestamps = false;

    protected $fillable = [
        'descripcion_estado',
        'vigente',
        'name_class'
    ];
}
