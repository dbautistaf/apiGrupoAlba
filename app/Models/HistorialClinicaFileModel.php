<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HistorialClinicaFileModel extends Model
{
    use HasFactory;
    protected $table = 'tb_historia_clinica_file';
    protected $primaryKey = 'id_file';
    public $timestamps = false;

    protected $fillable = [
        'url_file',
        'id_historia_clinica',
        'fecha_carga'
    ];

    protected $appends = ['url'];
    
    public function getUrlAttribute()
    {
        
        return asset('storage/historialclinico/' . $this->url_file);
    }

    public function historiaClinica()
    {
        return $this->belongsTo(HistoriaClinicaEntity::class, 'id_historia_clinica');
    }
}
