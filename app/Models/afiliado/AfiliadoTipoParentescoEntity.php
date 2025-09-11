<?php

namespace App\Models\afiliado;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AfiliadoTipoParentescoEntity extends Model
{
    use HasFactory;
    protected $table = 'tb_parentesco';
    protected $primaryKey = 'id_parentesco';
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'id_codigo',
        'parentesco'
    ];
}
