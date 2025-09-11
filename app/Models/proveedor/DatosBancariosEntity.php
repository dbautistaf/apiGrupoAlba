<?php

namespace App\Models\proveedor;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DatosBancariosEntity extends Model
{
    use HasFactory;
    protected $table = 'tb_proveedor_datos_bancarios';
    protected $primaryKey = 'cod_dato_bancario';
    public $timestamps = false;

    protected $fillable = [
        'numero_cuenta',
        'titular_cuenta',
        'tipo_cuenta',
        'cbu_cuenta',
        'cod_proveedor',
        'vigente'
    ];
}
