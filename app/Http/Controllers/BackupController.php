<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

class BackupController extends Controller
{
    public function backup()
    {
        try {
            // Ejecuta el comando de respaldo
            Artisan::call('backup:run');
            
            // Si el respaldo fue exitoso, retorna una respuesta JSON
            return response()->json([
                'status' => 'success',
                'message' => 'Respaldo creado con éxito',
            ]);
        } catch (\Exception $e) {
            // Si ocurre un error, retorna el error
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ]);
        }
    }
}
