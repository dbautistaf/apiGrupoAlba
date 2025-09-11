<?php

namespace App\Models\Soporte;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SoporteArchivoModelo extends Model
{
    use HasFactory;
    protected $connection = 'mysql_soporte';
    protected $table = 'tb_archivos';
    protected $primaryKey = 'id_archivo';
    public $timestamps = false;

    protected $fillable = [
        'id_ticket',
        'nombre_original',
        'ruta',
        'mime',
        'tamaño',
    ];

    // Relaciones
    public function ticket()
    {
        return $this->belongsTo(SoporteTicketsModelo::class, 'id_ticket', 'id_ticket');
    }
    public function getUrlAttribute()
    {
    return asset('storage/' . $this->ruta . '/' . $this->nombre_original);
    }


}
