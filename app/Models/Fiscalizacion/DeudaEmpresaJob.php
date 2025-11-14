<?php

namespace App\Models\Fiscalizacion;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\EmpresaModelo;


class DeudaEmpresaJob extends Model
{
    use HasFactory;

    protected $table = 'tb_fisca_deudas_job';

    protected $fillable = [
        'job_name',
        'started_at',
        'finished_at',
        'success',
        'message',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'finished_at' => 'datetime',
        'success' => 'boolean',
    ];
}
