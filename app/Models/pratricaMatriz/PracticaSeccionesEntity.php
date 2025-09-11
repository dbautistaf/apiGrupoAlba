<?php

namespace App\Models\pratricaMatriz;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PracticaSeccionesEntity extends Model
{
    use HasFactory;
    protected $table = 'tb_practica_secciones';
    protected $primaryKey = 'id_seccion';
    public $timestamps = false;

    protected $fillable = [
        'descripcion',
        'vigente',
        'id_nomenclador',
    ];
    public function nomenclador()
    {
        return $this->hasOne(PracticaNomencladorEntity::class, 'id_nomenclador', 'id_nomenclador');
    }
}
