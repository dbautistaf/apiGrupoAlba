<?php

namespace App\Models\Administracion;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CorrelativosOspfEntity extends Model
{
    use HasFactory;
    protected $table = 'tb_correlativos_ospf';
    protected $primaryKey = 'id_correlativo';
    public $timestamps = false;

    protected $fillable = [
        'numero',
        'abreviatura',
        'tipo_correlativo'
    ];
}
