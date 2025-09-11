<?php

namespace App\Models\auditoria;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AuditarFacturacionEntity extends Model
{
    use HasFactory;
    protected $table = 'log_facturacion';
    protected $primaryKey = 'id_auditor';
    public $timestamps = false;

    protected $fillable = [
        'nombre_tabla',
        'id_tabla',
        'data_anterior',
        'data_nueva',
        'cod_usuario',
        'tipo_accion',
        'fecha_accion'
    ];
}
