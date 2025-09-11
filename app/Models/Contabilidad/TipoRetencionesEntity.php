<?php

namespace App\Models\Contabilidad;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TipoRetencionesEntity extends Model
{
    use HasFactory;
    protected $table = 'tb_cont_tipo_retenciones';
    protected $primaryKey = 'id_retencion';
    public $timestamps = false;

    protected $fillable = [
        'descripcion',
        'vigente'
    ];
}
