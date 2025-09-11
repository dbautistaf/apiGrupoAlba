<?php

namespace App\Models\Protesis;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EstadoSolicitudProtesisEntity extends Model
{

    use HasFactory;
    protected $table = 'tb_protesis_estado_solicitud';
    protected $primaryKey = 'id_estado';
    public $timestamps = false;

    protected $fillable = [
        'descripcion',
        'vigente'
    ];
}
