<?php

namespace App\Models\liquidaciones;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LiqMatrizMedicamentosEntity extends Model
{
    use HasFactory;

    protected $table = 'tb_liquidaciones_matriz_medicamentos';
    protected $primaryKey = 'id_medicamento';
    public $timestamps = false;
    //--00001182
    protected $fillable = [
        'troquel',
        'registro',
        'nombre',
        'presentacion',
        'laboratorio',
        'droga',
        'accion',
        'acargo_ospf',
        'autorizacion_previa',
        'activo',
        'fecha_registra',
        'cod_usuario',
        'precio_venta',
        'precio_compra',
        'tipo_venta'
    ];
}
