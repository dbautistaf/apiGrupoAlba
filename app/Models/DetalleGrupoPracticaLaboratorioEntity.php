<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetalleGrupoPracticaLaboratorioEntity extends Model
{
    use HasFactory;
    protected $table = 'tb_detalle_practica_grupo';
    protected $primaryKey = 'cod_detalle_practica_grupo';
    public $timestamps = false;

    protected $fillable = [
        'cod_tipo_practica',
        'cod_practica_grupo'
    ];

    public function practica()
    {
        return $this->hasOne(TipoPracticasLaboratorioEntity::class, 'cod_tipo_practica', 'cod_tipo_practica');
    }

    public function grupo()
    {
        return $this->hasOne(TipoPracticasLaboratorioEntity::class, 'cod_tipo_practica', 'cod_tipo_practica');
    }
}
