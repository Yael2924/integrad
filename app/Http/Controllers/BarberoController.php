<?php

namespace App\Http\Controllers;

use App\Models\Barbero;
use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Routing\Controller;
use Barryvdh\DomPDF\Facade\Pdf;

class BarberoController extends Controller
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
        $busqueda = $request->busqueda;
        $lista = Barbero::where('nombre','like','%'.$busqueda.'%')->get();
        return view('barberos.index')->with(compact('lista','busqueda'));

        $barbe = Barbero::with('usuario')->get();
        return view('barberos.index', compact('barberos'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $usuarios = Usuario::where('rol', 'barbero')->get();
        return view('barberos.create', compact('usuarios'));

        // Obtener usuarios que no estén asociados a un barbero
        /*$usuarios = Usuario::whereDoesntHave('barbero') // Verifica que no exista relación con Barbero
        ->where('rol', 'barbero') // Solo usuarios con rol 'barbero'
        ->get();

        return view('barberos.create', compact('usuarios'));*/
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'usuario_id' => 'required|exists:usuarios,id|unique:barberos,usuario_id', // Validar usuario único
            'nombre' => [
                'required',
                'string',
                'min:10',
                'max:50',
                'regex:/^[A-Za-zÀ-ÿÑñ\s]+$/'
            ],
            'telefono' => [
                'required',
                'digits:10' // Asegura exactamente 10 dígitos
            ],
            'estado' => 'required|string|in:activo,inactivo', // Solo permite estos valores
        ], [
            'usuario_id.required' => 'El campo usuario es obligatorio.',
            'usuario_id.exists' => 'El usuario especificado no existe.',
            'usuario_id.unique' => 'Este usuario ya está registrado como barbero.',
            'nombre.required' => 'El campo nombre es obligatorio.',
            'nombre.string' => 'El nombre debe ser un texto.',
            'nombre.min' => 'El nombre debe contener al menos 10 caracteres.',
            'nombre.max' => 'El nombre no debe exceder 50 caracteres.',
            'nombre.regex' => 'El nombre solo puede contener letras y espacios, sin números ni caracteres especiales.',
            'telefono.required' => 'Debes proporcionar un número de teléfono.',
            'telefono.digits' => 'El teléfono debe contener exactamente 10 dígitos.',
            'estado.required' => 'Debes especificar el estado.',
            'estado.in' => 'El estado debe ser "activo" o "inactivo".'
        ]);

        $barbe = new Barbero; // Suponiendo que el modelo se llama Barbero
        $barbe->usuario_id = $request->usuario_id; // Relación con usuarios
        $barbe->nombre = $request->nombre;
        $barbe->telefono = $request->telefono;
        $barbe->estado = $request->estado === 'activo' ? 1 : 0;
        $barbe->save();

        return redirect(route('barberos.index'))->with('success', 'Barbero creado correctamente.');
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
        $barbe = Barbero::findOrFail($id);
        $usuarios = Usuario::where('rol', 'barbero')->get();
        return view('barberos.edit')->with(compact('barbe', 'usuarios'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'usuario_id' => 'required|exists:usuarios,id|unique:barberos,usuario_id,' . $id, // Ignorar el barbero actual
            'nombre' => [
                'required',
                'string',
                'min:10',
                'max:50',
                'regex:/^[A-Za-zÀ-ÿÑñ\s]+$/'
            ],
            'telefono' => [
                'required',
                'digits:10' // Asegura exactamente 10 dígitos
            ],
            'estado' => 'required|string|in:activo,inactivo', // Solo permite estos valores
        ], [
            'usuario_id.required' => 'El campo usuario es obligatorio.',
            'usuario_id.exists' => 'El usuario especificado no existe.',
            'usuario_id.unique' => 'Este usuario ya está registrado como barbero.',
            'nombre.required' => 'El campo nombre es obligatorio.',
            'nombre.string' => 'El nombre debe ser un texto.',
            'nombre.min' => 'El nombre debe contener al menos 10 caracteres.',
            'nombre.max' => 'El nombre no debe exceder 50 caracteres.',
            'nombre.regex' => 'El nombre solo puede contener letras y espacios, sin números ni caracteres especiales.',
            'telefono.required' => 'Debes proporcionar un número de teléfono.',
            'telefono.digits' => 'El teléfono debe contener exactamente 10 dígitos.',
            'estado.required' => 'Debes especificar el estado.',
            'estado.in' => 'El estado debe ser "activo" o "inactivo".'
        ]);

        $barbe = Barbero::find($id);
        $barbe->usuario_id = $request->usuario_id;
        $barbe->nombre = $request->nombre;
        $barbe->telefono = $request->telefono;
        $barbe->estado = $request->estado === 'activo' ? 1 : 0;
        $barbe->save();

        return redirect(route('barberos.index'))->with('success', 'Barbero actualizado correctamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $barbe = Barbero::find($id);
        $barbe->delete();

        return redirect(route('barberos.index'));
    }

    public function exportarPDF()
    {
        // Obtener usuario autenticado
        $usuario = Auth::user()->nombre;

        $lista = Barbero::all(); // Obtener todos los barberos
        // $pdf = Pdf::loadView('barberos.pdf', compact('lista'));
        // return $pdf->download('barberos.pdf');
        $pdf = Pdf::loadView('barberos.pdf', compact('lista', 'usuario'))
                ->setPaper('a4', 'portrait');
        return $pdf->stream('barberos.pdf');
    }
}