<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Producto;
use App\Models\Inventario;
use Carbon\Carbon;
use Illuminate\Routing\Controller;

class InventarioController extends Controller
{

    public function __construct(){
        $this->middleware(function ($request, $next) {
            if (Auth::check() && Auth::user()->rol == "Barbero") {
                abort(404, 'No tienes permiso para acceder al módulo de inventario.');
            }
            return $next($request);
        });

        $this->middleware(function ($request, $next) {
            if (Auth::check() && Auth::user()->rol != "Administrador") {
                abort(404, 'Solo los administradores pueden realizar esta acción.');
            }
            return $next($request);
        })->only(['destroy', 'create', 'store', 'edit', 'update', 'filtro', 'exportarPDF']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $busqueda = $request->busqueda;
        $producto_id = $request->producto_id; // Recibe el ID del producto
        $order = $request->get('order', 'desc');
        // Filtrar por ID de producto si se proporciona
        $lista = Inventario::when($producto_id, function ($query, $producto_id) {
            return $query->where('producto_id', $producto_id);
        })
        ->where('fecha', 'like', '%' . $busqueda . '%')
        ->orderBy('fecha', $order) // Ordenar por fecha
        ->with('producto')->get();

        return view('inventarios.index', compact('lista', 'busqueda', 'producto_id', 'order'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $producto_id = $request->producto_id; // Recibe el ID del producto
        $productos = Producto::all();
        $producto = Producto::find($producto_id);
        return view('inventarios.create', compact('productos', 'producto_id', 'producto'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'producto_id' => 'required|exists:productos,id',
            'fecha' => 'required|date',
            'stock' => 'required|integer|min:1'
        ]);
    
        // Crear nuevo registro en la tabla inventarios
        $inventario = Inventario::create($request->all());
    
        // Buscar el producto y sumar el nuevo stock al stock existente
        $producto = Producto::findOrFail($request->producto_id);
        $producto->stock += $request->stock;
        $producto->save(); // Guardar la actualización en la base de datos
    
        return redirect()->route('inventarios.index', ['producto_id' => $producto->id])
                ->with('success', 'Inventario registrado y stock actualizado correctamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $inventario = Inventario::findOrFail($id);
        $productos = Producto::all();
        return view('inventarios.edit', compact('inventario', 'productos'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request,$id)
    {
        $request->validate([
            'producto_id' => 'required|exists:productos,id',
            'fecha' => 'required|date',
            'stock' => 'required|integer|min:1'
        ]);

        $inventario = Inventario::findOrFail($id);
        $inventario->update($request->all());

        return redirect()->route('inventarios.index')->with('success', 'Inventario actualizado correctamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $inventario = Inventario::findOrFail($id);
        $inventario->delete();

        return redirect()->route('inventarios.index')->with('success', 'Inventario eliminado correctamente.');
    }


    public function filtro(Request $request)
    {
        // Obtener todos los productos ordenados alfabéticamente
        $productos = Producto::orderBy('nombre', 'asc')->get();
        $inventarios = collect(); // Inicializar una colección vacía para no obtener resultados si no hay filtros
        $totalStock = 0;

        // Solo realizar la consulta si algún filtro está presente
        $query = Inventario::query();

        // Filtro por nombre de producto
        if ($request->filled('busqueda')) {
            $busqueda = $request->busqueda;
            $query->whereHas('producto', function ($query) use ($busqueda) {
                $query->where('nombre', 'like', '%' . $busqueda . '%');
            });
        }

        // Filtro por fechas
        if ($request->filled('fecha_inicio') && $request->filled('fecha_fin')) {
            $fecha_inicio = Carbon::parse($request->fecha_inicio)->startOfDay();
            $fecha_fin = Carbon::parse($request->fecha_fin)->endOfDay();
            $query->whereBetween('fecha', [$fecha_inicio, $fecha_fin]);
        } elseif ($request->filled('fecha_inicio')) {
            $fecha_inicio = Carbon::parse($request->fecha_inicio)->startOfDay();
            $query->where('fecha', '>=', $fecha_inicio);
        } elseif ($request->filled('fecha_fin')) {
            $fecha_fin = Carbon::parse($request->fecha_fin)->endOfDay();
            $query->where('fecha', '<=', $fecha_fin);
        }

        // Filtro por producto
        if ($request->filled('producto_id')) {
            $query->where('producto_id', $request->producto_id);
        }

        // Filtro por stock mínimo y máximo
        if ($request->filled('stock_min')) {
            $query->where('stock', '>=', $request->stock_min);
        }
        if ($request->filled('stock_max')) {
            $query->where('stock', '<=', $request->stock_max);
        }

        // Verificar si al menos uno de los filtros fue aplicado
        if ($request->filled('busqueda') || $request->filled('fecha_inicio') || $request->filled('fecha_fin') || 
            $request->filled('producto_id') || $request->filled('stock_min') || $request->filled('stock_max')) {
            
            // Obtener los resultados filtrados y ordenados
            $inventarios = $query->orderBy('fecha', 'desc')->paginate(10);

            // Calcular el total de stock filtrado
            $totalStock = $inventarios->sum('stock');
        }

        // Retornar la vista con los datos filtrados
        return view('inventarios.filtro', compact('productos', 'inventarios', 'totalStock'));
    }

    public function exportarPDF(Request $request)
    {
        // Obtener usuario autenticado
        $usuario = Auth::user()->nombre;

        // Construir la consulta de inventarios con filtros
        $query = Inventario::query();

        if ($request->filled('producto_id')) {
            $query->where('producto_id', $request->producto_id);
        }

        if ($request->filled('fecha_inicio') && $request->filled('fecha_fin')) {
            $query->whereBetween('fecha', [$request->fecha_inicio, $request->fecha_fin]);
        }

        if ($request->filled('stock_min') && $request->stock_min >= 0) {
            $query->where('stock', '>=', $request->stock_min);
        }

        if ($request->filled('stock_max')) {
            $query->where('stock', '<=', $request->stock_max);
        }

        // Obtener los inventarios filtrados
        $lista = $query->orderBy('fecha', 'desc')->get();

        // Cargar la vista del PDF con los datos filtrados
        $pdf = Pdf::loadView('inventarios.pdf', compact('lista', 'usuario'))
                ->setPaper('a4', 'portrait');

        return $pdf->stream('reporte_inventarios.pdf');
    }

}
