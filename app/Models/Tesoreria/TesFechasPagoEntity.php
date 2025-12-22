<?php

namespace App\Models\Tesoreria;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TesFechasPagoEntity extends Model
{
    use HasFactory;

    protected $table = 'tb_tes_fecha_pago';
    protected $primaryKey = 'id_fecha_pago';
    public $timestamps = false;

    protected $fillable = [
        'fecha_confirma_pago',
        'id_orden_pago',
    ];
}	