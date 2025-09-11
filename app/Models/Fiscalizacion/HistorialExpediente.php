<?php

namespace App\Models\Fiscalizacion;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class HistorialExpediente extends Model
{
    protected $table = 'tb_fisca_historial_expedientes'; // o el nombre que uses
    protected $primaryKey = 'id_historial'; // asumido
    public $timestamps = false; // si no usás created_at / updated_at

    protected $fillable = [
        'id_expediente',
        'id_deuda',
        'periodo',
        'monto_deuda',
        'fecha_creacion',
    ];

    public function expediente()
    {
        return $this->belongsTo(Expediente::class, 'id_expediente');
    }
}
