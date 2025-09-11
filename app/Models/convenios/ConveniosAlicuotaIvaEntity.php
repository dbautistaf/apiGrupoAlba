<?php

namespace App\Models\convenios;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConveniosAlicuotaIvaEntity extends Model
{
    use HasFactory;
    protected $table = 'tb_convenios_alicuota_iva';
    protected $primaryKey = 'id_alicuota_iva';
    public $timestamps = false;

    protected $fillable = [
        'descripcion',
        'vigente'
    ];
}
