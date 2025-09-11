<?php

namespace App\Models\Protesis;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetalleComprobanteProtesis extends Model
{
    use HasFactory;
    protected $table = 'tb_protesis_detalle_comprobante';
    protected $primaryKey = 'id_comprobante';
    public $timestamps = false;

    protected $fillable = [
        'nombre_archivo',
        'fecha_registra',
        'activo',
        'id_protesis'
    ];
}
