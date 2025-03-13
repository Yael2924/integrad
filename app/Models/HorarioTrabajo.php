<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HorarioTrabajo extends Model
{
    protected $table = 'horarios_trabajo';
    
    protected $fillable = [
        'dia',
        'hora'
    ];

}
