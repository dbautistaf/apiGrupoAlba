<?php

namespace App\Models\configuracion;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RazonSocialModelo extends Model
{
    use HasFactory;
    protected $table = 'tb_razones_sociales';
    protected $primaryKey = 'id_razon';
    public $timestamps = false;

    protected $fillable = [
        'razon_social',
        'activo',
    ];
}
