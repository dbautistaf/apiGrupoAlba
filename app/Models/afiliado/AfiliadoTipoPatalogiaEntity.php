<?php

namespace App\Models\Afiliado;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AfiliadoTipoPatalogiaEntity extends Model
{
    use HasFactory;
    protected $table = 'tb_patalogia';
    protected $primaryKey = 'id_patologia';
    public $timestamps = false;

    protected $fillable = [
        'patalogia'
    ];
}
