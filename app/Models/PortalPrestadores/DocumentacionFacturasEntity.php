<?php

namespace App\Models\PortalPrestadores;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocumentacionFacturasEntity extends Model
{
    use HasFactory;

    protected $table = 'tb_portal_prestador_documentacion_factura';
    protected $primaryKey = 'id_documento';
    public $timestamps = false;

    protected $fillable = [
        'id_factura',
        'documento',
        'fecha_carga'
    ];
}
