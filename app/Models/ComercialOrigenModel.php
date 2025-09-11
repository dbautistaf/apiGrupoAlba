<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ComercialOrigenModel extends Model
{
    use HasFactory;
    protected $table = 'tb_comercial_origen';
    protected $primaryKey = 'id_comercial_origen';
    public $timestamps = false;

    protected $fillable = [
        'detalle_comercial_origen',
        'id_comercial_caja',
        'id_locatario',
        'activo'
    ];

    public function comercial_caja(){
        return $this->hasOne(ComercialCajaModel::class,'id_comercial_caja', 'id_comercial_caja');
    }

    public function locatario(){
        return $this->hasOne(LocatorioModelos::class,'id_locatorio', 'id_locatario');
    }
}
