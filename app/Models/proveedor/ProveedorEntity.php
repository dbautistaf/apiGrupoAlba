<?php

namespace App\Models\proveedor;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProveedorEntity extends Model
{
    use HasFactory;
    protected $table = 'tb_provedores_discapacidad';
    protected $primaryKey = 'cod_provedor';
    public $timestamps = false;

    protected $fillable = [
        'razon_social',
        'cuit'
    ];
}
