<?php
namespace App\Models\NubeComprobantes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NubeComprobantesEntity extends Model
{
    use HasFactory;
    protected $table = 'tb_nube_comprobantes';
    protected $primaryKey = 'id_comprobante';
    public $timestamps = false;

    protected $fillable = [
        'cuit',
        'nro_factura',
        'periodo',
        'nombre_archivo',
        'fecha_subida',
        'cod_usuario_registra'
    ];
}
