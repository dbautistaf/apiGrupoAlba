<?php

namespace App\Models\Tesoreria;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TesEstadoPagoEntity extends Model
{
    use HasFactory;

    protected $table = 'tb_tes_estado_pago';
    protected $primaryKey = 'id_estado_pago';
    public $timestamps = false;

    protected $fillable = [
        'descripcion_estado',
        'name_class',
        'vigente'
    ];
}
