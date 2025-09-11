<?php

namespace App\Models\Protesis;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class  CondicionProtesisEntity extends Model
{

    use HasFactory;
    protected $table = 'tb_protesis_condicion';
    protected $primaryKey = 'id_condicion';
    public $timestamps = false;

    protected $fillable = [
        'descripcion',
        'vigente'
    ];
}
