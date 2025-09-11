<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TipoQrModelo extends Model
{
    use HasFactory;
    protected $table = 'tb_tipo_qr';
    protected $primaryKey = 'id_qr';
    public $timestamps = false;

    protected $fillable = [
        'tipo_qr',
        'activo'
    ];
}
