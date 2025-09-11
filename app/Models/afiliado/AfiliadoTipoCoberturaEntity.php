<?php

namespace App\Models\afiliado;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AfiliadoTipoCoberturaEntity extends Model
{
    use HasFactory;
    protected $table = 'tb_cobertura';
    protected $primaryKey = 'id_cobertura';
    public $timestamps = false;

    protected $fillable = [
        'cobertura'
    ];
}
