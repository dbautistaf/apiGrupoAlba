<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransaccionesDetalleModel extends Model
{
    use HasFactory;
    use HasFactory;
    protected $table = 'tb_transacciones_detalle';
    protected $primaryKey = 'id_transacciones_detalle';
    public $timestamps = false;

    protected $fillable = [
        'linea',
        'registro',
        'troquel',
        'nombre',
        'cantidad',
        'cobertura',
        'precio_vigente',
        'id_transacciones'
    ];

    public function transaccion()
    {
        return $this->belongsTo(TransaccionesModel::class, 'id_transacciones');
    }
}
