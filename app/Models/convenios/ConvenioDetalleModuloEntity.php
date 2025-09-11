<?php

namespace App\Models\convenios;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConvenioDetalleModuloEntity extends Model
{
    use HasFactory;
    protected $table = 'tb_convenios_detalle_modulo';
    protected $primaryKey = 'id_modulo_detalle';
    public $timestamps = false;

    protected $fillable = [
        'id_modulo',
        'detalle',
        'fecha_crea'
    ];
}
