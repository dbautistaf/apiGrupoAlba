<?php

namespace App\Models\Fiscalizacion;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SeguimientoAnterior extends Model
{
    use HasFactory;

    // Nombre de la tabla en la base de datos
    protected $table = 'tb_fisca_seguimiento_anterior';

    // Clave primaria de la tabla
    protected $primaryKey = 'id';

    // Desactivar timestamps si la tabla no tiene columnas `created_at` y `updated_at`
    public $timestamps = false;

    // Campos que se pueden llenar masivamente (fillable)
    protected $fillable = [
        'nro',
        'nror',
        'cuit',
        'razon_social',
        'celular',
        'movimiento',
        'atendido',
        'derivado',
        'fecha',
        'hora',
        'sector',
        'observ',
        'finalizado',
        'email_estudio',
        'email_fcia',
        'tel_estudio',
        'tel_fcia',
        'localidad',
        'direccion',
        'delegacion',
        'institucion',
        'deuda_ospf',
        'deuda_fatfa',
        'usuario',
        'fe_inspeccion',
        'fe_notificacion',
        'baja_fcia',
        'fe_vto',
        'estado',
        'fecha_modif',
        'usuario_modif',
        'hora_modif',
        'fe_ini_prorroga',
        'fe_vto_prorroga',
        'observ_prorroga'
    ];

    // Relaciones (si aplica)
    // Ejemplo: Si hay una relación con otra tabla, puedes agregarla aquí.
}