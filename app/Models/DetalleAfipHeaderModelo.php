<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetalleAfipHeaderModelo extends Model
{
    use HasFactory;
    protected $table = 'tb_detalle_afip_header';
    protected $primaryKey = 'id_detalle';
    public $timestamps = false;

    protected $fillable = [
        'header_txt',
        'fecha_proceso'
    ];
}
