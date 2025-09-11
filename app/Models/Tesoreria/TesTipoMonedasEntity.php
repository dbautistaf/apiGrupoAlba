<?php

namespace App\Models\Tesoreria;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TesTipoMonedasEntity extends Model
{
    use HasFactory;

    protected $table = 'tb_tes_tipo_moneda';
    protected $primaryKey = 'id_tipo_moneda';
    public $timestamps = false;

    protected $fillable = [
        'descripcion_moneda',
        'vigente',
    ];
}
