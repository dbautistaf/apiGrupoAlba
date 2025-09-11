<?php

namespace App\Models\Fiscalizacion;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TipoMovimiento extends Model
{
    use HasFactory;

    protected $table = 'tb_fisca_tipo_movimientos'; // nombre real de la tabla
    protected $primaryKey = 'id_tipo_movimiento';
    public $timestamps = false;

    protected $fillable = [
        'descripcion',
        // otros campos si los hubiera
    ];
}