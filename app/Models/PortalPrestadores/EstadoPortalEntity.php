<?php

namespace App\Models\PortalPrestadores;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EstadoPortalEntity extends Model
{
    use HasFactory;
    protected $table = 'tb_portal_prestador_estados_factura';
    protected $primaryKey = 'id_estado';
    public $timestamps = false;

    protected $fillable = [
        'estado',
        'clase_css',
        'icon'
    ];
}
