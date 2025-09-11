<?php

namespace App\Models\Soporte;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SoporteTicketsModelo extends Model
{
    use HasFactory;
    protected $connection = 'mysql_soporte';
    protected $table = 'tb_tickets';
    protected $primaryKey = 'id_ticket';
    public $timestamps = false;

    protected $fillable = [
        // 'titulo',
        'observaciones',
        'asignado_por',
        'fecha_apertura',
        'fecha_respuesta',
        'cliente',
        'id_prioridad',
        'id_tipo_producto',
        'id_estado',
        'id_instancia',
        'id_categoria',
        'id_Usuario',
        'id_encargado'
    ];

    public function Estado()
    {
        return $this->hasOne(SoporteEstadoModelo::class, 'id_estado', 'id_estado');
    }

    public function Prioridad()
    {
        return $this->hasOne(SoportePrioridadModelo::class, 'id_prioridad', 'id_prioridad');
    }

    public function Instancia()
    {
        return $this->hasOne(SoporteInstanciaModelo::class, 'id_instancia', 'id_instancia');
    }
    public function Categoria()
    {
        return $this->hasOne(SoporteCategoriaModelo::class, 'id_categoria', 'id_categoria');
    }
    public function Asignados()
    {
        return $this->hasOne(SoporteUsuarioSoporteModelo::class, 'id_encargado', 'id_encargado');
    }
    public function historial()
    {
        return $this->hasMany(SoporteHistorialTicketModelo::class, 'id_ticket', 'id_ticket');
    }
    public function archivo()
    {
        return $this->hasMany(SoporteArchivoModelo::class, 'id_ticket', 'id_ticket');
    }
}
