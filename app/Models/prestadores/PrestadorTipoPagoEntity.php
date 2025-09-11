<?php

namespace App\Models\prestadores;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PrestadorTipoPagoEntity extends Model
{
    use HasFactory;
    protected $table = 'tb_prestador_tipo_pago';
    protected $primaryKey = 'id_tipo_pago';
    public $timestamps = false;

    protected $fillable = [
        'tipo_pago',
        'vigente'
    ];
}
