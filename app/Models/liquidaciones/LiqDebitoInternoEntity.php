<?php
namespace App\Models\liquidaciones;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LiqDebitoInternoEntity extends Model{
    use HasFactory;

    protected $table = 'tb_liquidaciones_debito_interno';
    protected $primaryKey = 'id_debito';
    public $timestamps = false;

    protected $fillable = [
        'id_factura',
        'nombre_archivo',
        'fecha_carga',
        'cod_usuario_crea',
        'observaciones',
        'tipo'
    ];
}
