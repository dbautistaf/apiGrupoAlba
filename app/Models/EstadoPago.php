<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EstadoPago extends Model
{
    use HasFactory;
    protected $table = 'tb_estado_pago';
    protected $primaryKey = 'id_estado_pago';
    public $timestamps = false;

    protected $fillable = [
        'detalle_pago',
        'estado'
    ];
}
