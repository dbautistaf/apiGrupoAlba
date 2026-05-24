<?php

namespace App\Models\Internaciones;

use App\Models\DetallePrestacionesPracticaLaboratorioEntity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InternacionAutorizacionEntity extends Model
{
    use HasFactory;
    protected $table = 'tb_internaciones_autorizacion';
    protected $primaryKey = 'id_internacion_autorizacion';
    public $timestamps = false;
    protected $fillable = [
        'cod_internacion',
        'cod_prestacion',
        'fecha_registra',
        'cod_usuario'
    ];

    public function detalle_prestacion()
    {
        return $this->hasMany(DetallePrestacionesPracticaLaboratorioEntity::class, 'cod_prestacion', 'cod_prestacion');
    }
}
