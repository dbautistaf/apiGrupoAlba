<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeledagosModelo extends Model
{
    use HasFactory;
    protected $table = 'tb_delegacion';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'delegacion'
    ];
}
