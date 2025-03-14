<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Configuracion extends Model
{
    use HasFactory;

    // Nombre de la tabla en la base de datos
    protected $table = "configuraciones";
    // Los campos que pueden ser asignados de manera masiva
    protected $fillable = ['porcentaje_ganancia'];
}
