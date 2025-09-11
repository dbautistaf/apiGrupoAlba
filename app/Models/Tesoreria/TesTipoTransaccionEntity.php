<?php

namespace App\Models\Tesoreria;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TesTipoTransaccionEntity extends Model
{
    use HasFactory;

    protected $table = 'tb_tes_tipo_transaccion';
    protected $primaryKey = 'id_tipo_transaccion';
    public $timestamps = false;

    protected $fillable = [
        'descripcion',
        'vigente',
    ];
}
