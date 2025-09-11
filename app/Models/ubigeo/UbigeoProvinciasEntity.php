<?php

namespace App\Models\ubigeo;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UbigeoProvinciasEntity extends Model
{
    use HasFactory;
    protected $table = 'tb_ubigeo_provincias';
    protected $primaryKey = 'cod_provincia';
    public $timestamps = false;

    protected $fillable = [
        'nombre_provincia',
        'vigente'
    ];
}
