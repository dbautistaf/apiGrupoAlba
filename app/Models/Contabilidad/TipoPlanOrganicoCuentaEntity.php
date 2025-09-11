<?php

namespace App\Models\Contabilidad;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TipoPlanOrganicoCuentaEntity extends Model
{
    use HasFactory;
    protected $table = 'tb_cont_tipo_plan_organico_cuenta';
    protected $primaryKey = 'id_tipo_cuenta';
    public $timestamps = false;

    protected $fillable = [
        'descripcion',
        'vigente'
    ];
}
