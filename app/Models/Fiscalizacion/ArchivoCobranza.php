<?php

namespace App\Models\Fiscalizacion;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ArchivoCobranza extends Model
{
    use HasFactory;
    protected $table = 'tb_fisca_comprobantes_cobranza';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'id_cobranza',
        'nombre_original',
        'ruta',
        'tipo_archivo',
        'tamaño',
        'fecha_subida',
    ];

    // Relaciones
    public function cobranza()
    {
        return $this->belongsTo(Cobranza::class, 'id_cobranza', 'id_cobranza');
    }
    public function getUrlAttribute()
    {
    return asset('storage/' . $this->ruta . '/' . $this->nombre_original);
    }


}
