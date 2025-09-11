<?php

namespace App\Models\Contabilidad;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NivelesPlanCuentaEntity extends Model
{
    use HasFactory;
    protected $table = 'tb_cont_tipo_niveles_plan_cuenta';
    protected $primaryKey = 'id_tipo_nivel_plan_cuenta';
    public $timestamps = false;

    protected $fillable = [
        'descripcion',
        'vigente'
    ];
}
