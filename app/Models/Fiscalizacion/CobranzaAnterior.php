<?php

namespace App\Models\Fiscalizacion;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CobranzaAnterior extends Model
{
    use HasFactory;

    // Nombre de la tabla en la base de datos
    protected $table = 'tb_fisca_cobranzas_anterior';

    // Clave primaria de la tabla
    protected $primaryKey = 'id';

    // Desactivar timestamps si la tabla no tiene columnas `created_at` y `updated_at`
    public $timestamps = false;

    // Campos que se pueden llenar masivamente (fillable)
    protected $fillable = [
        'nro',
        'cuit',
        'razon_social',
        'fecha',
        'usuario',
        'importe',
        'acta',
        'observaciones',
        'medio_pago',
        'num_transf',
        'num_cuota',
        'num_cheque',
        'comprobante',
        'comprobante2',
        'comprobante3',
        'comprobante4',
        'fecha_pago',
        'en_concepto',
        'filial',
        'num_cuota_paga',
        'plan_pago',
        'importe_sueldo',
        'aporte',
        'contribucion',
        'contribucion_extraordinaria',
        'aporte_extraordinario',
        'intereses',
        'id_banco',
        'banco',
        'firmante',
        'tot_os',
        'tot_gastos_os',
        'organizacion',
        'organizacion_id',
        'secuencia',
        'liquidado',
        'genera_comision',
        'recibo',
        'deuda_reclamada',
        'diferencia',
        'neto',
        'indexacion',
        'bonificacion',
        'administrativo'
    ];

    // Relaciones (si aplica)
    // Ejemplo: Si hay una relación con otra tabla, puedes agregarla aquí.
}