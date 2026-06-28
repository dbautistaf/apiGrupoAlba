<?php

namespace App\Models\PortalPrestadores;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LogComentariosFacturaEntity extends Model
{
    use HasFactory;
    protected $table = 'tb_portal_prestador_comentarios';
    protected $primaryKey = 'id_logs';
    public $timestamps = false;

    protected $fillable = [
        'id_estado',
        'comentario_prestador',
        'comentario_interno',
        'fecha_carga',
        'cod_usuario',
        'id_factura'
    ];
}
