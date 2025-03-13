<?php

namespace App\Http\Controllers;

use App\Models\VentaProducto;
use Illuminate\Http\Request;
use App\Models\Producto;
use App\Models\Venta;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Routing\Controller;
use Barryvdh\DomPDF\Facade\Pdf;

class VentaProductoController extends Controller
{

    public function __construct(){
        $this->middleware(function ($request, $next) {
            if (Auth::check() && Auth::user()->rol == "Barbero") {
                abort(404, 'No tienes permiso para acceder al módulo de usuarios.');
            }
            return $next($request);
        });

        $this->middleware(function ($request, $next) {
            if (Auth::check() && Auth::user()->rol != "Administrador") {
                abort(404, 'Solo los administradores pueden realizar esta acción.');
            }
            return $next($request);
        })->only(['destroy', 'create', 'store', 'edit', 'update']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $busqueda = $request->busqueda; // Ahora $request está definido correctamente

        // Filtrar por ID solo si se proporciona una búsqueda
        $lista = Venta::when($busqueda, function ($query, $busqueda) {
            return $query->where('id', $busqueda);
        })->orderBy('fecha_hora', 'desc')->paginate(10);

        return view('ventas_productos.index', compact('lista', 'busqueda'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $productos = Producto::all();
        $ventas = Venta::all();
        return view('ventas_productos.create')->with(compact('productos','ventas'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validar los datos enviados
        $request->validate([
            'productos' => 'required|array',
            'productos.*.cantidad' => 'required|integer|min:1',
            'total_general' => 'required|numeric|min:0',
        ]);
    
        // Crear la venta
        $venta = Venta::create([
            'total' => $request->input('total_general'),
            'fecha_hora' => now(),
        ]);
    
        // Agregar los productos a la venta
        foreach ($request->input('productos') as $productoId => $producto) {
            $productoModel = Producto::find($productoId);
    
            // Validar que el producto exista y que haya suficiente stock
            if ($productoModel && $producto['cantidad'] <= $productoModel->stock) {
                $subtotal = $producto['cantidad'] * $productoModel->precio; // Calcular subtotal
    
                // Agregar a la tabla pivote
                $venta->productos()->attach($productoId, [
                    'cantidad' => $producto['cantidad'],
                    'precio_unitario' => $productoModel->precio,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
    
                // Reducir el stock del producto
                $productoModel->decrement('stock', $producto['cantidad']);
            } else {
                return back()->withErrors(['error' => 'Stock insuficiente para el producto: ' . $productoModel->nombre]);
            }
        }
    
        return redirect()->route('ventas_productos.index')
            ->with('success', 'Venta registrada correctamente.');
    }
    
    public function detalles($id)
    {
        // Buscar la venta por ID con los productos asociados
        $venta = Venta::with('productos')->withTrashed()->find($id);

        if (!$venta) {
            return abort(404, 'Venta no encontrada');
        }
    
        // Calcular subtotales dinámicamente
        foreach ($venta->productos as $producto) {
            $producto->subtotal_calculado = $producto->pivot->cantidad * $producto->pivot->precio_unitario;
        }
    
        return view('ventas_productos.detalles', compact('venta'));
    }
    
    public function filtro(Request $request)
    {    
        // Si la validación pasa, filtra las ventas y calcula la ganancia
        $ventas = [];
        $totalVentas = 0;
    
        // Convertir fechas para incluir todo el rango del día
        $fecha_inicio = Carbon::parse($request->fecha_inicio)->startOfDay();
        $fecha_fin = Carbon::parse($request->fecha_fin)->endOfDay();
    
        // Filtrar ventas por rango de fechas
        $query = Venta::whereBetween('fecha_hora', [$fecha_inicio, $fecha_fin]);
    
        // Obtener los datos
        $ventas = $query->get();
        $totalVentas = $ventas->sum('total');
    
        // Retornar vista con los datos
        return view('ventas_productos.filtro', compact('ventas', 'totalVentas'));
    }
    
    public function exportarPDF(Request $request)
    {

        // Validar que las fechas están presentes
        $request->validate([
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'required|date',
        ]);

        // Convertir las fechas al formato correcto
        $fecha_inicio = Carbon::parse($request->fecha_inicio)->startOfDay();
        $fecha_fin = Carbon::parse($request->fecha_fin)->endOfDay();
        
    
        // Filtrar ventas por rango de fechas
        $ventas = Venta::whereBetween('fecha_hora', [$fecha_inicio, $fecha_fin])->get();
    
        $totalVentas = $ventas->sum('total');

        // Obtener usuario autenticado
        $usuario = Auth::user()->nombre;
    
        // Cargar la vista para el PDF
        $pdf = Pdf::loadView('ventas_productos.pdf', compact( 'ventas', 'totalVentas', 'fecha_inicio', 'fecha_fin', 'usuario'))
                  ->setPaper('a4', 'portrait');
    
        return $pdf->stream('reporte_ventas_productos.pdf');
    }
    
    public function exportarRecibo($id)
    {

        // Obtener usuario autenticado
        $usuario = Auth::user()->nombre;

        // Obtener la venta con sus productos
        $venta = Venta::with('productos')->withTrashed()->findOrFail($id);
    
        // Calcular subtotales dinámicamente
        foreach ($venta->productos as $producto) {
            $producto->subtotal_calculado = $producto->pivot->cantidad * $producto->pivot->precio_unitario;
        }
    
        // Generar el PDF
        $pdf = Pdf::loadView('ventas_productos.recibo_pdf', compact('venta', 'usuario'))
                    ->setPaper('a4', 'portrait');; // 'recibo' es el nombre de la vista del recibo
    
        // Retornar el PDF como descarga
        return $pdf->stream('recibo_venta_' . $venta->id . '.pdf');
    }
    

    /**
     * Display the specified resource.
     */
    public function show(VentaProducto $ventaProducto)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(VentaProducto $ventaProducto)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, VentaProducto $ventaProducto)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(VentaProducto $ventaProducto)
    {
        //
    }
}
