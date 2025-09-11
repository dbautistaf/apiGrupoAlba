<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DiscaPacidadDetalleModel extends Model
{
    use HasFactory;
    protected $table = 'tb_discapacidad_detalle';
    protected $primaryKey = 'id_discapacidad_detalle';
    public $timestamps = false;

    protected $fillable = [
        'id_practica',
        'cantidad',
        'dependencia',
        'id_discapacidad',
        'subsidio'
    ];

    public function practica()
    {
        return $this->hasOne(PracticasDiscaPacidadModel::class, 'id_practica', 'id_practica');
    }

    public function disca()
    {
        return $this->belongsTo(IntegracionDiscapacidadModel::class,'id_discapacidad','id_discapacidad');
    }

    public function subsidiodisca()
    {
        return $this->hasOne(SubsidiosDiscapacidadModel::class, 'id_discapacidad_detalle', 'id_discapacidad_detalle');
    }



}
