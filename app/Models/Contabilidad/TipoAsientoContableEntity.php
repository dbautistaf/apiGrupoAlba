<?php

namespace App\Models\Contabilidad;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TipoAsientoContableEntity extends Model
{
    use HasFactory;
    protected $table = 'tb_cont_asientos_tipo';
    protected $primaryKey = 'id_tipo_asiento';
    public $timestamps = false;

    protected $fillable = [
        'tipo_asiento',
        'vigente'
    ];
}
