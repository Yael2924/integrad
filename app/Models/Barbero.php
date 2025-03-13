<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Barbero extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = "barberos";

    protected $fillable = [
        'usuario_id',
        'nombre',
        'telefono',
        'disponibilidad',
        'estado',
    ];

    protected $casts = [
        'disponibilidad' => 'array',
    ];

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'usuario_id');
    }

    public function venta_servicio()
    {
        return $this->hasMany(VentaServicio::class, 'barbero_id');
    }

    public function citas()
    {
        return $this->hasMany(Cita::class);
    }

}
