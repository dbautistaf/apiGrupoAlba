<?php

namespace App\Models\convenios;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConveniosTipoMedioPagoEntity extends Model
{
    use HasFactory;
    protected $table = 'tb_convenios_tipo_valor_pago';
    protected $primaryKey = 'id_tipo_valor_pago';
    public $timestamps = false;

    protected $fillable = [
        'descripcion',
        'vigente'
    ];
}
