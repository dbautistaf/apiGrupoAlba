<?php

namespace App\Models\Protesis;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrigenMaterialProtesisEntity extends Model
{

    use HasFactory;
    protected $table = 'tb_protesis_origen_material';
    protected $primaryKey = 'id_origen_material';
    public $timestamps = false;

    protected $fillable = [
        'descripcion',
        'vigente'
    ];
}
