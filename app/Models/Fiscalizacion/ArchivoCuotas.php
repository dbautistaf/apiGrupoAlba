<?php

namespace App\Models\Fiscalizacion;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ArchivoCuotas extends Model
{
    use HasFactory;
    protected $table = 'tb_fisca_comprobantes_cuotas_acuerdo';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'id_cuota',
        'nombre_original',
        'ruta',
        'tipo_archivo',
        'tamaño',
        'fecha_subida',
    ];

    // Relaciones
    public function cuota()
    {
        return $this->belongsTo(Cuota::class, 'id_cuota', 'id_cuota');
    }
    public function getUrlAttribute()
    {
    return asset('storage/' . $this->ruta . '/' . $this->nombre_original);
    }


}
