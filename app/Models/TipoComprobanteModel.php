<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TipoComprobanteModel extends Model
{
    use HasFactory;

    protected $table = 'tb_tipo_comprobante';
    protected $primaryKey = 'id_tipo_comprobante';
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'id_tipo_comprobante',
        'tipo_comprobante',
        'vigente'
    ];


}
