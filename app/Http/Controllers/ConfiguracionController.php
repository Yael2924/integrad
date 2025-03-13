<?php

namespace App\Http\Controllers;

use App\Models\Configuracion;
use Illuminate\Http\Request;

class ConfiguracionController extends Controller
{
    // Método para mostrar la vista de configuración
    public function index()
    {
        $configuracion = Configuracion::first(); // Obtener la configuración existente
        return view('configuracion.index', compact('configuracion')); // Retorna la vista con los datos
    }

    // Método para guardar la nueva configuración de porcentaje de ganancia
    public function guardar(Request $request)
    {
        // Validar que el porcentaje esté entre 0 y 100
        $request->validate([
            'porcentaje' => 'required|numeric|min:0|max:100',
        ]);

        // Obtener la configuración o crear una nueva si no existe
        $configuracion = Configuracion::first() ?? new Configuracion();
        $configuracion->porcentaje_ganancia = $request->input('porcentaje');
        $configuracion->save(); // Guardar el cambio

        // Redirigir con un mensaje de éxito
        return redirect()->route('configuracion.index')->with('success', 'Porcentaje de ganancia actualizado');
    }
}
