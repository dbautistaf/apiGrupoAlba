<?php

namespace App\Models\Protesis;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProtesisMatrizDiagnosticoEntity extends Model
{

    use HasFactory;
    protected $table = 'tb_protesis_matriz_diagnosticos';
    protected $primaryKey = 'identificador';
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'identificador',
        'descripcion',
        'fecha_crea',
        'cod_usuario',
        'fecha_actualiza',
        'vigente'
    ];
}
