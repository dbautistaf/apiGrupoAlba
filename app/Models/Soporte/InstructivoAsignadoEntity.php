<?php

namespace   App\Models\Soporte;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InstructivoAsignadoEntity extends Model
{
    use HasFactory;

    protected $table = 'tb_instructivos_perfil';
    protected $primaryKey = 'id_asigna';
    public $timestamps = false;

    protected $fillable = [
        'id_perfil',
        'id_instructivo',
        'vigente'
    ];

    public function instructivo()
    {
        return $this->hasOne(InstructivosEntity::class, 'id_instructivo', 'id_instructivo');
    }
}
