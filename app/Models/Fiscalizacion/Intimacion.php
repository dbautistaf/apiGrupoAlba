<?php

namespace App\Models\Fiscalizacion;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\EmpresaModelo;
use App\Models\User;

class Intimacion extends Model
{
    use HasFactory;

    protected $table = 'tb_fisca_seguimiento_intimacion';
    protected $primaryKey = 'id_intimacion';
    public $timestamps = false;

    protected $fillable = [
        'id_intimacion',
        'atendido_por',
        'celular_farmacia',
        'derivado',
        'direccion',
        'email_estudio',
        'email_farmacia',
        'fecha_inicio_gestion',
        'fecha_vencimiento_gestion',
        'id_usuario',
        'localidad',
        'nombre_usuario',
        'numero_registro',
        'observaciones',
        'sector',
        'telefono_Estudio',
        'telefono_farmacia',
        'total_deuda_fatfa',
        'total_deuda_ospf',
        'id_sindicato',
        'id_empresa',
        'id_institucion',
        'id_tipo_movimiento',
        'usuario',
        'estado',
        'id_expediente',

    ];

    // Relaciones opcionales (si existen modelos relacionados)
    public function empresa()
    {
        return $this->belongsTo(\App\Models\EmpresaModelo::class, 'id_empresa', 'id_empresa');
    }

    public function tipoMovimiento()
    {
        return $this->belongsTo(\App\Models\Fiscalizacion\TipoMovimiento::class, 'id_tipo_movimiento', 'id_tipo_movimiento');
    }
    public function institucion()
    {
        return $this->belongsTo(\App\Models\Fiscalizacion\Institucion::class, 'id_institucion', 'id_institucion');
    }
    public function expediente()
    {
        return $this->belongsTo(Expediente::class, 'id_expediente', 'id_expediente');
    }
    
}
