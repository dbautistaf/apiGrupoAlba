<?php

namespace App\Models\Fiscal;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CobranzaPeriodo extends Model
{
    use HasFactory;

    protected $table = 'tb_fisca_cobranza_periodo';
    protected $primaryKey = 'id_cobranza_periodo';
    public $timestamps = false;

    protected $fillable = [
        'id_cobranza',
        'id_periodo',
        'monto_asociado'
    ];

    // Relaciones
    public function cobranza()
    {
        return $this->belongsTo(Cobranza::class, 'id_cobranza', 'id_cobranza');
    }

    public function periodo()
    {
        return $this->hasOne(Periodo::class, 'id_periodo', 'id_periodo');
    }
}