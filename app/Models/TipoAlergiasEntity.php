<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TipoAlergiasEntity extends Model
{
    use HasFactory;
    protected $table = 'tb_tipo_alergias';
    protected $primaryKey = 'cod_tipo_alergia';
    public $timestamps = false;

    protected $fillable = [
        'descripcion',
        'vigente'
    ];
}
