<?php

namespace App\Models\configuracion;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TipoPlanGalenosEntity extends Model
{
    use HasFactory;
    protected $table = 'tb_conf_galeno_plan';
    protected $primaryKey = 'id_conf_galeno_plan';
    public $timestamps = false;

    protected $fillable = [
        'descripcion',
        'vigente'
    ];

    protected $hidden = [
        'pivot',
    ];
}
