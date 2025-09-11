<?php

namespace   App\Models\Soporte;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SoporteEstadoModelo extends Model
{
    use HasFactory;
    protected $connection = 'mysql_soporte';
    protected $table = 'tb_estado';
    protected $primaryKey = 'id_estado';
    public $timestamps = false;

    protected $fillable = [
        'estado_tipo'
    ];

    public function historialesAnteriores()
    {
        return $this->hasMany(SoporteHistorialTicketModelo::class, 'estado_anterior', 'id_estado');
    }

    public function historialesNuevos()
    {
        return $this->hasMany(SoporteHistorialTicketModelo::class, 'estado_nuevo', 'id_estado');
    }
}
