<?php

namespace   App\Models\Soporte;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SoporteHistorialTicketModelo extends Model
{
    use HasFactory;
    // Especifica la conexión (si usas múltiples bases de datos)
    protected $connection = 'mysql_soporte';

    // Nombre de la tabla
    protected $table = 'tb_historial_tickets';

    // Clave primaria
    protected $primaryKey = 'id_historial';

    // Desactivar timestamps si no usas created_at y updated_at
    public $timestamps = false;

    // Campos que pueden ser asignados masivamente
    protected $fillable = [
        'id_ticket',
        'estado_anterior',
        'estado_nuevo',
        'fecha_cambio',
        'id_encargado_anterior',
        'id_encargado_nuevo',
        'id_usuario',
        'comentario'
    ];

    // Relaciones
    public function ticket()
    {
        return $this->belongsTo(SoporteTicketsModelo::class, 'id_ticket', 'id_ticket');
    }

    public function estadoAnterior()
    {
        return $this->hasOne(SoporteEstadoModelo::class, 'id_estado', 'estado_anterior');
    }

    public function estadoNuevo()
    {
        return $this->hasOne(SoporteEstadoModelo::class, 'id_estado', 'estado_nuevo');
    }

    public function encargadoAnterior()
    {
        return $this->hasOne(SoporteUsuarioSoporteModelo::class, 'id_encargado', 'id_encargado_anterior');
    }

    public function encargadoNuevo()
    {
        return $this->hasOne(SoporteUsuarioSoporteModelo::class, 'id_encargado', 'id_encargado_nuevo');
    }
}
