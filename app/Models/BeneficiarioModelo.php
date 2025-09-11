<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BeneficiarioModelo extends Model
{
    use HasFactory;
    protected $table = 'tb_beneficiario';
    protected $primaryKey = 'id_tipo_beneficiario';
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'tipo'
    ];
}
