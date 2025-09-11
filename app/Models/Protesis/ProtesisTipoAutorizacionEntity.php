<?php

namespace App\Models\Protesis;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProtesisTipoAutorizacionEntity extends Model
{
    use HasFactory;
    protected $table = 'tb_protesis_tipo_autorizacion';
    protected $primaryKey = 'id_tipo_autorizacion';
    public $timestamps = false;

    protected $fillable = [
        'descripcion',
        'vigente'
    ];
}
