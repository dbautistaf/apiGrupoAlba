<?php

namespace App\Models\Contabilidad;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TipoImpuestoEntity extends Model
{
    use HasFactory;

    protected $table = 'tb_cont_tipo_impuesto';
    protected $primaryKey = 'id_impuesto';
    public $timestamps = false;

    protected $fillable = [
        'descripcion',
        'vigente',
    ];


}
