<?php

namespace App\Models\prestadores;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DatosBancariosPrestadorEntity extends Model
{
    use HasFactory;
    protected $table = 'tb_datos_bancarios_prestador';
    protected $primaryKey = 'cod_banco_empresa';
    public $timestamps = false;

    protected $fillable = [
        'numero_cuenta',
        'titular_cuenta',
        'tipo_cuenta',
        'cbu_cuenta',
        'cbu_cuenta1',
        'cbu_cuenta2',
        'vigente',
        'cod_prestador',
        'cuit_prestador'
    ];
}
