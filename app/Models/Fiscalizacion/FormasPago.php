<?php

namespace App\Models\Fiscalizacion;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class FormasPago extends Model
{
    use HasFactory;
    
    protected $table = 'tb_fisca_forma_pago';
    protected $primaryKey = 'id_forma_pago';
    public $timestamps = false;

    protected $fillable = [
        'id_forma_pago',
        'descripcion'
    ];
}