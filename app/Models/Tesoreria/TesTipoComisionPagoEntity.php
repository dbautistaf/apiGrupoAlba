<?php

namespace App\Models\Tesoreria;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TesTipoComisionPagoEntity extends Model
{
    use HasFactory;

    protected $table = 'tb_tes_comisiones_pago';
    protected $primaryKey = 'id_comision';
    public $timestamps = false;

    protected $fillable = [
        'nombre',
        'porcentaje',
    ];
}
