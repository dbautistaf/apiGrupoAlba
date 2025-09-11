<?php

namespace App\Models\Fiscalizacion;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EstadoAcuerdo extends Model
{
    use HasFactory;

    protected $table = 'tb_fisca_estado_acuerdo';
    protected $primaryKey = 'id_estado_acuerdo';
    public $timestamps = false;

    protected $fillable = [
        'descripcion'
    ];

    public function acuerdos()
    {
        return $this->hasMany(AcuerdoPago::class, 'id_estado_acuerdo', 'id_estado_acuerdo');
    }
}
