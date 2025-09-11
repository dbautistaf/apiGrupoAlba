<?php

namespace App\Models\arca;

use App\Models\EmpresaModelo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DomicilioExplotacionModel extends Model
{
    use HasFactory;
    protected $table = 'tb_domicilios_explotacion';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'cuit_empleador',
        'codigo_movimiento',
        'tipo_externo',
        'calle',
        'numero_puerta',
        'torre',
        'bloque',
        'piso',
        'departamento',
        'codigo_postal',
        'localidad',
        'provincia',
        'sucursal',
        'actividad',
        'fecha_hora_movimiento',
        'area_reservada',
        'fecha_proceso',
        'id_usuario'
    ];

    public function Empresa()
    {
        return $this->hasOne(EmpresaModelo::class, 'cuit', 'cuit_empleador');
    }
}
