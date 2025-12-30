<?php

namespace App\Models\Tesoreria;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TesFechaProbablePagoEntity extends Model
{
    use HasFactory;
    protected $table = 'tb_tes_fecha_probable_pago';
    protected $primaryKey = 'id_fecha_probable';
    public $timestamps = false;

    protected $fillable = [
        'fecha_probable_pago',
        'orden_cuotas',
        'fecha_registra',
        'id_pago',
    ];
}
