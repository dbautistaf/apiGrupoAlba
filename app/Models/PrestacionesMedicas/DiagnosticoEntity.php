<?php

namespace App\Models\PrestacionesMedicas;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DiagnosticoEntity extends Model
{
    use HasFactory;
    protected $table = 'tb_prestaciones_diagnostico';
    protected $primaryKey = 'id_diagnostico';
    public $timestamps = false;

    protected $fillable = [
        'descripcion',
        'estado',
        'fecha_registra',
        'cod_usuario'
    ];

}
