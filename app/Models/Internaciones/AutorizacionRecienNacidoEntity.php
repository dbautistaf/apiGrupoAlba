<?php

namespace App\Models\Internaciones;

use App\Models\DetallePrestacionesPracticaLaboratorioEntity;
use App\Models\PrestacionesPracticaLaboratorioEntity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AutorizacionRecienNacidoEntity extends Model
{
    use HasFactory;
    protected $table = 'tb_internaciones_autorizacion_rn';
    protected $primaryKey = 'id_internacion_autorizacion_rn';
    public $timestamps = false;
    protected $fillable = [
        'cod_recien_nacido',
        'cod_prestacion',
        'fecha_registra',
        'cod_usuario'
    ];

    public function detalle_prestacion()
    {
        return $this->hasMany(DetallePrestacionesPracticaLaboratorioEntity::class, 'cod_prestacion', 'cod_prestacion');
    }

    public function internacion()
    {
        return $this->hasOne(PrestacionesPracticaLaboratorioEntity::class, 'cod_prestacion', 'cod_prestacion');
    }
}
