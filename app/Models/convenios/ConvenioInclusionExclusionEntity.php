<?php

namespace App\Models\convenios;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConvenioInclusionExclusionEntity extends Model
{
    use HasFactory;
    protected $table = 'tb_convenios_exclusion_inclusion';
    protected $primaryKey = 'id_inclu_exclu';
    public $timestamps = false;

    protected $fillable = [
        'observaciones',
        'archivo',
        'id_convenio'
    ];
}
