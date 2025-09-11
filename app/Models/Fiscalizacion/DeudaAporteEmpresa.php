<?php

namespace App\Models\Fiscalizacion;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\EmpresaModelo;

class DeudaAporteEmpresa extends Model
{
    use HasFactory;
    
    protected $table = 'tb_fisca_deudas_aportes_empresa';
    protected $primaryKey = 'id_deuda';
    public $timestamps = false; // Porque tu tabla no tiene created_at ni updated_at

    protected $fillable = [
        'anio',
        'importe_sueldo',
        'aporte',
        'contribucion',
        'contribucion_extraordinaria',
        'fecha_recalculo',
        'fecha_vencimiento',
        'id_empresa',
        'intereses',
        'mes',
        'monto_deuda',
        'monto_estudio_juridico',
        'monto_gestion_morosidad',
        'tipo_deuda',
        'estado',
    ];

    // Relación con la empresa
    public function empresa()
    {
        return $this->belongsTo(EmpresaModelo::class, 'id_empresa', 'id_empresa');
    }
}
