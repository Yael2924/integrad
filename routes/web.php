<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ServicioController;
use App\Http\Controllers\BarberoController;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\VentaServicioController;
use App\Http\Controllers\VentaProductoController;
use App\Http\Controllers\ClimaController;
use App\Http\Controllers\CitaController;
use App\Http\Controllers\BackupController;
use App\Http\Controllers\ConfiguracionController;
use App\Http\Controllers\InventarioController;

// RUTA PARA GENERAR UN BACKUP DE LA BASE DE DATOS
Route::post('/backup', [BackupController::class, 'backup'])->name('backup.create');

Route::get('/', function () {
    if (Auth::check()) {
        return redirect()->route('ventas.index'); // Redirige a Ventas si está autenticado
    }
    return view('inicio'); // Muestra la página de inicio si no está autenticado
})->name('inicio');

Route::get('/dashboard', [VentaServicioController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('ventas.index');

// Route::get('/bienvenido', [VentaProductoController::class, 'index'])->name('bienvenido');

// Route::get('/', function () {
//     return view('inicio');
// });

// // Route::get('/', [ClimaController::class, 'obtenerClima'])->name('clima.obtener'); //PARA OBTENER EL CLIMA

// Route::get('/dashboard', function () {
//     return view('inicio');
// })->middleware(['auth', 'verified'])->name('dashboard');


Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/servicios/exportar-pdf', [ServicioController::class, 'exportarPDF'])->name('servicios.exportarPDF'); //PARA EXPORTAR LOS SERVICIOS A PDF

    Route::resource('servicios', ServicioController::class); //PARA LOS SERVICIOS

    Route::get('/ventas_servicios/porcentaje', [VentaServicioController::class, 'porcentaje'])->name('ventas_servicios.porcentaje'); //PARA EL REPORTE DE VENTAS DE SERVICIOS POR PORCENTAJE Y BARBEROS

    Route::get('/ventas_servicios/filtro', [VentaServicioController::class, 'filtro'])->name('ventas_servicios.filtro'); //PARA EL REPORTE DE VENTAS DE SERVICIOS POR FILTRO

    Route::post('/ventas_servicios/exportarPDF', [VentaServicioController::class, 'exportarPDF'])->name('ventas_servicios.exportarPDF'); //PARA EXPORTAR LAS VENTAS DE SERVICIOS A PDF

    Route::get('/ventas_servicios/{id}/pdf', [VentaServicioController::class, 'generarRecibo'])->name('ventas_servicios.pdf'); //PARA GENERAR EL RECIBO DE LAS VENT

    Route::resource('ventas_servicios', VentaServicioController::class); //PARA LAS VENTAS DE SERVICIOS

    Route::get('/ventas_productos/filtro', [VentaProductoController::class, 'filtro'])->name('ventas_productos.filtro'); //PARA EL REPORTE DE VENTAS DE PRODUCTOS POR FILTRO

    Route::post('/ventas_productos/exportarPDF', [VentaProductoController::class, 'exportarPDF'])->name('ventas_productos.exportarPDF'); //PARA EXPORTAR LAS VENTAS DE PRODUCTOS A PDF

    Route::get('ventas_productos/{id}/recibo', [VentaProductoController::class, 'exportarRecibo'])->name('ventas_productos.pdf'); //PARA EXPORTAR EL RECIBO DE LA VENTA DE PRODUCTOS

    Route::resource('ventas_productos', VentaProductoController::class); //PARA LAS VENTAS DE PRODUCTOS

    Route::get('/ventas_productos/{id}/detalles', [VentaProductoController::class, 'detalles'])->name('ventas_productos.detalles'); //PARA VER LOS DETALLES DE LAS VENTAS DE PRODUCTOS

    Route::get('/barberos/exportar-pdf', [BarberoController::class, 'exportarPDF'])->name('barberos.exportarPDF'); //PARA EXPORTAR LOS BARBEROS A PDF
    
    Route::resource('barberos', BarberoController::class); //PARA LOS BARBEROS

    Route::get('/usuarios/exportar-pdf', [UsuarioController::class, 'exportarPDF'])->name('usuarios.exportarPDF'); //PARA EXPORTAR LOS USUARIOS A PDF

    Route::resource('usuarios', UsuarioController::class); //PARA LOS USUARIOS

    Route::get('/productos/exportar-pdf', [ProductoController::class, 'exportarPDF'])->name('productos.exportarPDF'); //PARA EXPORTAR LOS PRODUCTOS A PDF

    Route::resource('productos', ProductoController::class); //PARA LOS PRODUCTOS

    Route::get('/inventarios/exportar-pdf', [InventarioController::class, 'exportarPDF'])->name('inventarios.exportarPDF'); //PARA EXPORTAR EL INVENTARIO A PDF

    Route::get('/inventarios/filtro', [InventarioController::class, 'filtro'])->name('inventarios.filtro'); //PARA EL REPORTE DE INVENTARIOS POR FILTRO

    Route::resource('inventarios', InventarioController::class); //PARA EL INVENTARIO

    // Rutas para las citas
    Route::get('/citas/filtro', [CitaController::class, 'filtro'])->name('citas.filtro'); //PARA EL REPORTE DE CITAS POR FILTRO

    Route::post('/citas/exportarPDF', [CitaController::class, 'exportarPDF'])->name('citas.exportarPDF'); //PARA EXPORTAR LAS CITAS A PDF
    
    Route::resource('citas', CitaController::class);

    Route::get('get-horas-disponibles', [CitaController::class, 'getHorasDisponibles']); // Ruta para obtener las horas disponibles

    ////////////////////////////////           PARA LOS EDITAR MI PERFIL             //////////////////////////////////////////

    Route::get('usuarios/{id}/edit_sinrol', [UsuarioController::class, 'editSinRol'])->name('usuarios.edit_sinrol');

    Route::put('usuarios/{id}/update_sinrol', [UsuarioController::class, 'updateSinRol'])->name('usuarios.update_sinrol');

    //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    
    ////////////////////////////////           PARA LAS CONFIGURACIONES             //////////////////////////////////////////

    Route::get('/configuracion', [ConfiguracionController::class, 'index'])->name('configuracion.index');
    Route::post('/configuracion/guardar', [ConfiguracionController::class, 'guardar'])->name('configuracion.guardar');

    //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    
});


###############################################################################################################################
######################                   RUTAS PARA LO DE LA APLICACION MOVIL                    ##############################
###############################################################################################################################


/** SERVICIOS WEB **/
Route::get('api/login', [UsuarioController::class,'login']);
// Route::post('api/registrar', [UsuarioController::class, 'registrar']);


Route::middleware('auth:api')->group( function() {
    // Route::resource('api/carreras', CarreraController::class); 
    // Route::get('api/citas', [CitaController::class,'citas']); 
});

require __DIR__.'/auth.php';
