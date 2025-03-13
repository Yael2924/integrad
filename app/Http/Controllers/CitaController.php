<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cita;
use App\Models\Usuario;
use App\Models\Barbero;
use App\Models\HorarioTrabajo;
use App\Models\HorarioExcepcion;
use Illuminate\Validation\Rule;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Routing\Controller;

use Illuminate\Support\Facades\Log;


class CitaController extends Controller
{
    public function __construct(){
        $this->middleware(function ($request, $next) {
            if (Auth::check() && Auth::user()->rol == "Barbero") {
                abort(404, 'No tienes permiso para acceder al módulo de usuarios.');
            }
            return $next($request);
        });

        // $this->middleware(function ($request, $next) {
        //     if (Auth::check() && Auth::user()->rol != "Administrador") {
        //         abort(404, 'Solo los administradores pueden realizar esta acción.');
        //     }
        //     return $next($request);
        // })->only(['destroy', 'create', 'store', 'edit', 'update']);
    }

    /**
     * Display a listing of the resource.
     */
//     public function index(Request $request)
// {
//     $busqueda = $request->busqueda;

//     // 🔹 OBTENER LAS FECHAS ÚNICAS Y PAGINARLAS (con groupBy en lugar de distinct)
//     $fechas = Cita::select('fecha')
//         ->groupBy('fecha') // Agrupamos por fecha para asegurarnos de que sean únicas
//         ->orderBy('fecha', 'desc')
//         ->paginate(1); // 👈 Se paginan SOLO las fechas

//     // 🔹 OBTENER LAS CITAS DE LA FECHA ACTUALMENTE PAGINADA
//     $citas = Cita::with(['usuario', 'barbero'])
//         ->whereIn('fecha', $fechas->pluck('fecha')) // 👈 Filtramos por la fecha actual paginada
//         ->when($busqueda, function ($query, $busqueda) {
//             return $query->where(function ($subquery) use ($busqueda) {
//                 $subquery->whereHas('usuario', function ($q) use ($busqueda) {
//                     $q->where('nombre', 'like', '%' . $busqueda . '%');
//                 })
//                 ->orWhereHas('barbero', function ($q) use ($busqueda) {
//                     $q->where('nombre', 'like', '%' . $busqueda . '%');
//                 })
//                 ->orWhere('hora', 'like', '%' . $busqueda . '%');
//             });
//         })
//         ->orderBy('hora', 'asc')
//         ->get(); // 👈 Aquí NO paginamos las citas, solo filtramos por la fecha paginada

//     // 🔹 ACTUALIZAR ESTADO DE CITAS PASADAS
//     foreach ($citas as $cita) {
//         $fechaHoraCita = Carbon::parse($cita->fecha . ' ' . $cita->hora);
//         $ahora = Carbon::now();

//         if ($ahora->greaterThanOrEqualTo($fechaHoraCita->addHour()) && $cita->estado !== 'Completado') {
//             $cita->estado = 'Completado';
//             $cita->save();
//         }
//     }

//     return view('citas.index', compact('citas', 'fechas', 'busqueda'));
// }


    public function index()
    {
        // 🔹 OBTENER LAS FECHAS ÚNICAS Y PAGINARLAS (con groupBy en lugar de distinct)
        $fechas = Cita::select('fecha')
            ->groupBy('fecha') // Agrupamos por fecha para asegurarnos de que sean únicas
            ->orderBy('fecha', 'desc')
            ->paginate(1); // 👈 Se paginan SOLO las fechas

        // 🔹 OBTENER LAS CITAS DE LA FECHA ACTUALMENTE PAGINADA
        $citas = Cita::with(['usuario', 'barbero'])
            ->whereIn('fecha', $fechas->pluck('fecha')) // 👈 Filtramos por la fecha actual paginada
            ->orderBy('hora', 'asc')
            ->get(); // 👈 Aquí NO paginamos las citas, solo filtramos por la fecha paginada

        // 🔹 ACTUALIZAR ESTADO DE CITAS PASADAS
        foreach ($citas as $cita) {
            $fechaHoraCita = Carbon::parse($cita->fecha . ' ' . $cita->hora);
            $ahora = Carbon::now();

            if ($ahora->greaterThanOrEqualTo($fechaHoraCita->addHour()) && $cita->estado !== 'Completado') {
                $cita->estado = 'Completado';
                $cita->save();
            }
        }

        return view('citas.index', compact('citas', 'fechas'));
    }

    /**
     * Show the form for creating a new resource.
     */
    // Mostrar el formulario de creación de cita
    public function create()
    {
        $barberos = Barbero::where('estado', 1)->get(); // Obtener solo los barberos con estado igual a ACTIVO
        $clientes = Usuario::where('rol', 'Cliente')->get();
        return view('citas.create', compact('barberos', 'clientes'));
    }

    // Función para obtener las horas disponibles según el día
    public function getHorasDisponibles(Request $request)
    {
        // Establecer el idioma a español para Carbon
        Carbon::setLocale('es');

        $fecha = $request->input('fecha');
        $barbero_id = $request->input('barbero_id');

        // Verifica que los parámetros recibidos estén correctos
        Log::debug('Fecha recibida  FUNCION GET:', [$fecha]);
        Log::debug('Barbero ID recibido  FUNCION GET:', [$barbero_id]);

        // Obtener el día de la semana de la fecha seleccionada en español
        $diaSemana = Carbon::parse($fecha)->locale('es')->isoFormat('dddd');
        Log::debug('Día de la semana:', [$diaSemana]);

        // Obtener los horarios disponibles del barbero y día seleccionado
        $horariosTrabajo = HorarioTrabajo::where('dia', $diaSemana)->get();
        Log::debug('Horarios de trabajo obtenidos  FUNCION GET:', [$horariosTrabajo]);

        // Si estamos editando una cita, excluimos la hora de la cita actual
        if (isset($id)) {
            $horasOcupadas = Cita::where('fecha', $fecha)
                ->where('barbero_id', $barbero_id)
                ->where('id', '!=', $id) // Excluye la cita que estamos editando
                ->pluck('hora')
                ->toArray();
        } else {
            // Si no estamos editando, obtenemos todas las horas ocupadas
            $horasOcupadas = Cita::where('fecha', $fecha)
                ->where('barbero_id', $barbero_id)
                ->pluck('hora')
                ->toArray();
        }


        // Filtrar las horas disponibles (restar las horas ocupadas de los horarios de trabajo)
        $horariosDisponibles = $horariosTrabajo->filter(function ($horario) use ($horasOcupadas) {
            return !in_array($horario->hora, $horasOcupadas);
        });

        // Convertir la colección de Eloquent a un arreglo simple
        $horariosDisponibles = $horariosDisponibles->values()->toArray();
        
        Log::debug('Horarios disponibles FUNCION GET:', [$horariosDisponibles]);

        // Devolver la respuesta en formato JSON
        return response()->json($horariosDisponibles);
    }

    
    // Función para guardar la cita
    public function store(Request $request)
    {
        $validated = $request->validate([
            'usuario_id' => 'required|exists:usuarios,id',
            'barbero_id' => 'required|exists:barberos,id',
            'fecha' => 'required|date',
            'hora' => 'required',
        ],[
            'usuario_id.required' => 'El campo usuario es obligatorio.',
            'barbero_id.required' => 'El campo barbero es obligatorio.',
            'fecha.required' => 'El campo fecha es obligatorio.',
            'hora.required' => 'El campo hora es obligatorio.',
        ]);

        // Crear la cita
        $cita = new Cita;
        $cita->usuario_id = $request->usuario_id;
        $cita->barbero_id = $request->barbero_id;
        $cita->fecha = $request->fecha;
        $cita->hora = $request->hora;
        $cita->estado = 'Pendiente';
        $cita->save();

        return redirect()->route('citas.index')->with('success', 'Cita creada exitosamente');
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
        $cita = Cita::findOrFail($id);
        $barberos = Barbero::where('estado', 1)->get(); // Obtener solo los barberos con estado igual a ACTIVO
        $clientes = Usuario::where('rol', 'Cliente')->get();
        return view('citas.edit', compact('cita','barberos', 'clientes'));
    }    

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'usuario_id' => 'required|exists:usuarios,id',
            'barbero_id' => 'required|exists:barberos,id',
            'fecha' => 'required|date',
            'hora' => 'required',
        ],[
            'usuario_id.required' => 'El campo usuario es obligatorio.',
            'barbero_id.required' => 'El campo barbero es obligatorio.',
            'fecha.required' => 'El campo fecha es obligatorio.',
            'hora.required' => 'El campo hora es obligatorio.',
        ]);

        // Actualizar la cita
        $cita = Cita::find($id);
        $cita->usuario_id = $request->usuario_id;
        $cita->barbero_id = $request->barbero_id;
        $cita->fecha = $request->fecha;
        $cita->hora = $request->hora;
        $cita->estado = 'Pendiente';
        $cita->save();

        return redirect()->route('citas.index')->with('success', 'Cita reagendada exitosamente');
    }

    public function filtro(Request $request)
    {    
        // Obtener todos los barberos, incluyendo los eliminados con SoftDeletes
        // Obtener barberos que tienen ventas asociadas
        $barberos = Barbero::whereHas('citas', function($query) {
            $query->whereNotNull('fecha');
        })->withTrashed()->orderBy('nombre', 'asc')->get();

        $usuarios = Usuario::whereHas('citas', function($query) {
            $query->whereNotNull('fecha');
        })->withTrashed()->orderBy('nombre', 'asc')->get();

        $citas = [];

        // Convertir fechas para incluir todo el rango del día
        $fecha_inicio = Carbon::parse($request->fecha_inicio)->startOfDay();
        $fecha_fin = Carbon::parse($request->fecha_fin)->endOfDay();

        // Iniciar la consulta con el filtro de fechas
        $query = Cita::whereBetween('fecha', [$fecha_inicio, $fecha_fin]);

        // Aplicar filtro por cliente (si está presente)
        if ($request->filled('usuario_id')) {
            $query->where('usuario_id', $request->usuario_id);
        }

        // Aplicar filtro por barbero (si está presente)
        if ($request->filled('barbero_id')) {
            $query->where('barbero_id', $request->barbero_id);
        }

        // Aplicar filtro por estado (si está presente)
        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        // Obtener los datos filtrados
        $citas = $query->orderBy('fecha', 'desc') // Ordenar por fecha de la más reciente a la más antigua
                    ->paginate(10); // Paginar cada 10 elementos

        // Retornar la vista con los datos filtrados
        return view('citas.filtro', compact('barberos', 'usuarios', 'citas'));
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
        
        // Inicializar la consulta para las ventas
        $citasQuery = Cita::whereBetween('fecha', [$fecha_inicio, $fecha_fin]);
    
        // Aplicar otros filtros si existen
        if ($request->filled('usuario_id')) {
            $citasQuery->where('usuario_id', $request->usuario_id);
        }
    
        if ($request->filled('barbero_id')) {
            $citasQuery->where('barbero_id', $request->barbero_id);
        }
    
        if ($request->filled('estado')) {
            $citasQuery->where('estado', $request->estado);
        }
    
        // Obtener las ventas filtradas
        $citas = $citasQuery->get();
    
    
        // // Calcular el total de ventas filtradas
        // $totalVentas = $ventas->sum('total');
    
        // Obtener usuario autenticado
        $usuario = Auth::user()->nombre;
    
        // Cargar la vista para el PDF con solo los resultados filtrados
        $pdf = Pdf::loadView('citas.pdf', compact('citas', 'fecha_inicio', 'fecha_fin', 'usuario'))
                  ->setPaper('a4', 'portrait');
    
        return $pdf->stream('reporte_citas.pdf');
    }
    

    

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $cita = Cita::find($id);

        // Solo permite borrar si la cita está completada
        if ($cita->estado !== 'Completado') {
            return redirect()->route('citas.index')->with('error', 'Solo puedes eliminar citas completadas.');
        }

        $cita->delete();

        return redirect()->route('citas.index')->with('success', 'Cita eliminada correctamente.');
    }

    // private function obtenerHorariosDisponibles($barberoId, $fecha)
    // {
    //     // Obtener los horarios de trabajo del barbero
    //     $horariosTrabajo = HorarioTrabajo::where('barbero_id', $barberoId)->get();

    //     // Obtener las citas agendadas para la fecha y barbero seleccionados
    //     $citasOcupadas = Cita::where('barbero_id', $barberoId)
    //         ->where('fecha', $fecha)
    //         ->pluck('hora')
    //         ->toArray();

    //     // Calcular los horarios disponibles
    //     $horariosDisponibles = [];

    //     foreach ($horariosTrabajo as $horario) {
    //         $horaActual = strtotime($horario->hora_inicio);
    //         $horaFin = strtotime($horario->hora_fin);

    //         while ($horaActual < $horaFin) {
    //             $horaFormateada = date('H:i', $horaActual);

    //             // Verificar si la hora no está ocupada
    //             if (!in_array($horaFormateada, $citasOcupadas)) {
    //                 $horariosDisponibles[] = $horaFormateada;
    //             }

    //             // Añadir 30 minutos (o el intervalo deseado)
    //             $horaActual = strtotime('+30 minutes', $horaActual);
    //         }
    //     }

    //     return $horariosDisponibles;
    // }

    // public function obtenerHorariosDisponiblesAjax(Request $request)
    // {
    //     $barberoId = $request->query('barbero_id');
    //     $fecha = $request->query('fecha');

    //     if (!$barberoId || !$fecha) {
    //         return response()->json([]);
    //     }

    //     // Obtener los horarios disponibles
    //     $horariosDisponibles = $this->obtenerHorariosDisponibles($barberoId, $fecha);

    //     return response()->json($horariosDisponibles);
    // }
}
