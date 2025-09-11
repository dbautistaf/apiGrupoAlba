<?php

namespace App\Models\Diabetes;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetalleDiabetesEntity extends Model
{
    use HasFactory;
    protected $table = 'tb_padron_diabetes_detalle';
    protected $primaryKey = 'id_diabetes_detalle';
    public $timestamps = false;

    protected $fillable = [
        'id_diabetes',
        'id_medicamento',
        'fecha_inicio',
        'fecha_fin'
    ];

    public function medicamento()
    {
        return $this->hasOne(MedicamentosDiabetesEntity::class, 'id_medicamento', 'id_medicamento');
    }
}
