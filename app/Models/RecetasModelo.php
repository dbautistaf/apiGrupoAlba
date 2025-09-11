<?php

namespace App\Models;

use App\Models\afiliado\AfiliadoPadronEntity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RecetasModelo extends Model
{
    use HasFactory;
    protected $table = 'tb_recetas';
    protected $primaryKey = 'id_receta';
    public $timestamps = false;
    protected $fillable = [
        'numero_receta',
        'id_farmacia',
        'id_padron',
        'observaciones',
        'fecha_receta',
        'fecha_carga',
        'periodo',
        'medico',
        'caratula',
        'matricula',
        'colegio',
        'origen',
        'subtotal',
        'importe_total',
        'total_afiliado',
        'total_obra_social',
        'id_tipo_plan',
        'fecha_prescripcion',
        'lote',
        'validado',
        'id_usuario',
        'numero_validacion'
    ];

    public function Afiliado()
    {
        return $this->hasOne(AfiliadoPadronEntity::class,'id', 'id_padron');
    }

    public function Farmacia()
    {
        return $this->hasOne(FarmaciasModelo::class,'id_farmacia', 'id_farmacia');
    }

    public function detalleReceta()
    {
        return $this->hasMany(DetalleRecetasModelo::class, 'id_receta');
    }

}
