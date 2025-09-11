<?php

namespace App\Models\proveedor;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TipoProveedor extends Model
{
    use HasFactory;
    protected $table = 'tb_proveedor_tipo';
    protected $primaryKey = 'id_proveedor_tipo';
    public $timestamps = false;

    protected $fillable = [
        'detalle_tipo',
        'estado'
    ];
}
