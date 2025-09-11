<?php

namespace App\Models\pratricaMatriz;

use App\Models\CategoriaInternacionEntity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PracticaMatrizEntity extends Model
{
    use HasFactory;
    protected $table = 'tb_practicas_matriz';
    protected $primaryKey = 'id_identificador_practica';
    //protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'codigo_practica',
        'id_seccion',
        'nombre_practica',
        'cobertura',
        'coseguro',
        'vigente',
        'norma',
        'id_padre',
        'cod_categoria_internacion',
        'id_practica_valorizacion',
        'id_tipo_galeno',
        'especialista',
        'ayudante_cantidad',
        'ayudante',
        'anestesista',
        'galeno_gasto',
        'valor_gasto',
        'galeno_adicional',
        'valor_adicional',
        'galeno_aparatologia',
        'valor_aparatologia',
        'fecha_vigencia',
        'id_nivel',
        'id_tipo_valorizacion',
        'representa_unidad'
    ];

    public function seccion()
    {
        return $this->hasOne(PracticaSeccionesEntity::class, 'id_seccion', 'id_seccion');
    }

    public function padre()
    {
        return $this->hasOne(PracticaPadreEntity::class, 'id_padre', 'id_padre');
    }

    public function categoriaInternacion()
    {
        return $this->hasOne(CategoriaInternacionEntity::class, 'cod_categoria_internacion', 'cod_categoria_internacion');
    }

    public function practicaValorizacion()
    {
        return $this->hasOne(PracticaValorizacionEntity::class, 'id_practica_valorizacion', 'id_practica_valorizacion');
    }

    public function tipoGaleno()
    {
        return $this->hasOne(PracticaTipoGalenoEntity::class, 'id_tipo_galeno', 'id_tipo_galeno');
    }
}
