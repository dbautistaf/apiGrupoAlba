<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FarmanexusModelo extends Model
{
    use HasFactory;
    protected $table = 'tb_farmanexus';
    protected $primaryKey = 'id_farmanexus';
    public $timestamps = false;

    protected $fillable = [
        'cuf',
        'cuit',
        'razon_social',
        'nombre_fantasia',
        'provincia',
        'fecha_validacion',
        'numero_receta',
        'nro_item',
        'nro_afil',
        'afiliado',
        'edad',
        'producto',
        'cantidad',
        'precio_venta',
        'precio_venta_desc',
        'cod_validacion',
        'estado',
        'fecha_receta',
        'ppio_activo',
        'cobertura',
        'plan',
        'tipo_matricula',
        'numero_matricula',
        'medico',
        'registroab',
        'nrodoc_afiliado',
        'otro_costo',
        'laboratorio',
        'labo_id',
        'prestador',
        'presentacion_fcia',
        'id_externo',
        'recetario_orig',
        'fecha_proceso',
        'id_usuario',
    ];
}
