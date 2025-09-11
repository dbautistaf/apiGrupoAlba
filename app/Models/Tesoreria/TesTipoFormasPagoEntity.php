<?php

namespace App\Models\Tesoreria;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TesTipoFormasPagoEntity extends Model
{
    use HasFactory;

    protected $table = 'tb_tes_formas_pago';
    protected $primaryKey = 'id_forma_pago';
    public $timestamps = false;

    protected $fillable = [
        'id_comision',
        'tipo_pago',
    ];

    public function comision()
    {
        return $this->hasOne(TesTipoComisionPagoEntity::class, 'id_comision', 'id_comision');
    }
}
