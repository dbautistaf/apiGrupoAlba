<?php

namespace App\Models\Tesoreria;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TesEntidadesBancariasEntity extends Model
{
    use HasFactory;

    protected $table = 'tb_tes_entidades_bancarias';
    protected $primaryKey = 'id_entidad_bancaria';
    public $timestamps = false;

    protected $fillable = [
        'descripcion_banco',
        'vigente',
    ];
}
