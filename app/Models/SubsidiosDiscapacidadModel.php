<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubsidiosDiscapacidadModel extends Model
{
    use HasFactory;
    protected $table = 'tb_subsidios_discapacidad';
    protected $primaryKey = 'cod_subsidio';
    public $timestamps = false;

    protected $fillable = [
        'num_liquidacion',
        'importe_solicitado',
        'importe_subsidiado',
        'fecha_registra',
        'id_discapacidad_detalle'
    ];
}
