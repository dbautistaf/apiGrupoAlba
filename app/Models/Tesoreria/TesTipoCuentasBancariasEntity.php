<?php

namespace App\Models\Tesoreria;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TesTipoCuentasBancariasEntity extends Model
{
    use HasFactory;

    protected $table = 'tb_tes_tipo_cuentas_bancarias';
    protected $primaryKey = 'id_tipo_cuenta';
    public $timestamps = false;

    protected $fillable = [
        'descripcion_cuenta',
        'vigente',
    ];
}
