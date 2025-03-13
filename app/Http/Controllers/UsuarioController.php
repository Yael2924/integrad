<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash; 
use Illuminate\Validation\ValidationException; 
use Illuminate\Support\Facades\Auth;
use Illuminate\Routing\Controller;
use Illuminate\Validation\Rule;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;


class UsuarioController extends Controller
{

    public function __construct(){

        // Desactivar CSRF solo para esta acción

        // $this->middleware('csrf', ['except' => ['registrar']]);



        $this->middleware(function ($request, $next) {
            if (Auth::check() && Auth::user()->rol == "Barbero") {
                abort(404, 'No tienes permiso para acceder al módulo de usuarios.');
            }
            return $next($request);
        })->except(['editSinRol', 'updateSinRol']);

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

        // Obtener el ID del usuario autenticado
        $usuarioActualId = auth()->id();
        $lista = Usuario::where('id', '!=', $usuarioActualId)->where('nombre_usuario','like','%'.$busqueda.'%')->get();
        return view('usuarios.index')->with(compact('lista','busqueda'));

        $usuarios = Usuario::where('id', '!=', $usuarioActualId)->get();
        return view('usuarios.index', compact('usuarios'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $usuarios = Usuario::all();
        return view('usuarios.create', compact('usuarios'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => [
                'required',
                'string',
                'min:10',
                'max:50',
                'regex:/^[A-Za-zÀ-ÿÑñ\s]+$/'
            ],
            'nombre_usuario' => [
                'required',
                'min:5',
                'max:20',
                Rule::unique('usuarios', 'nombre_usuario')->whereNull('deleted_at')

            ],
            'email' => [
                'required', 
                'email', 
                'max:100', 
                Rule::unique('usuarios', 'email')->whereNull('deleted_at')
            ],
            'password' => ['required', 'string','min:6','confirmed',
                'regex:/[A-Z]/', 'regex:/[a-z]/', 'regex:/[0-9]/', 'regex:/[@$!%*?&]/',
            ],
            'telefono' => [
                'required', 
                'digits:10', 
                'unique:usuarios,telefono',
            ],

            'rol' => 'required|string|in:Administrador,Barbero,Cliente',
        ], [
            'nombre.required' => 'El campo nombre es obligatorio.',
            'nombre.min' => 'El nombre debe tener al menos 10 caracteres.',
            'nombre.max' => 'El nombre no debe exceder los 50 caracteres',
            'nombre.regex' => 'El nombre solo puede contener letras y espacios, sin números ni caracteres especiales.',

            'nombre_usuario.required' => 'El campo nombre de usuario es obligatorio.',
            'nombre_usuario.unique' => 'El nombre de usuario ya está en uso.',
            'nombre_usuario.min' => 'El nombre de usuario debe tener al menos 5 caracteres.',
            'nombre_usuario.max' => 'El nombre de usuario no debe exceder los 20 caracteres',

            'email.required' => 'El campo correo electrónico es obligatorio.',
            'email.unique' => 'El correo electrónico ya está registrado.',
            'email.max' => 'El correo no debe superar los 100 caracteres.',

            'password.required' => 'El campo contraseña es obligatorio.',
            'password.min' => 'La contraseña debe tener al menos 6 caracteres.',
            'password.regex' => 'La contraseña debe tener al menos una mayuscula, una minuscula, un numero y un caracter especial (@$!%*?&)',
            'password.confirmed' => 'Las contraseñas no coinciden.',

            'telefono.required' => 'El campo teléfono es obligatorio',
            'telefono.digits' => 'El teléfono deben ser 10 digitos',
            'telefono.unique' => 'El teléfono ya está registrado',

            'rol.required' => 'El campo rol es obligatorio.',
            'rol.in' => 'El rol seleccionado no es válido.',
        ]);

        // Crear el nuevo usuario
        $usuario = new Usuario();
        $usuario->nombre = $validated['nombre'];
        $usuario->nombre_usuario = $validated['nombre_usuario'];
        $usuario->email = $validated['email'];
        $usuario->password = bcrypt($validated['password']); // Encriptar la contraseña
        $usuario->telefono = $validated['telefono'];
        $usuario->rol = $validated['rol'];
        $usuario->save();

        return redirect(route('usuarios.index'))->with('success', 'Usuario creado correctamente.');
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
        $usuarioActual = Auth::user();
        if ($usuarioActual->rol !== 'Administrador' && $usuarioActual->id != $id) {
            abort(404, 'No tienes permiso para editar este usuario');
        }

        $usuario = Usuario::findOrFail($id);
        $usuarios = Usuario::all();
        return view('usuarios.edit', compact('usuario', 'usuarios'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $usuarioActual = Auth::user();
        if ($usuarioActual->rol !== 'Administrador' && $usuarioActual->id != $id) {
            abort(404, 'No tienes permiso para actualizar este usuario');
        }

        $usuario = Usuario::findOrFail($id);

        $validated = $request->validate([
            'nombre' => [
                'required',
                'string',
                'min:10',
                'max:50',
                'regex:/^[A-Za-zÀ-ÿÑñ\s]+$/'
            ],
            'nombre_usuario' => [
                'required',
                'min:5',
                'max:20',
                Rule::unique('usuarios', 'nombre_usuario')->whereNull('deleted_at')->ignore($id),
            ],
            'email' => [
                'required', 
                'email', 
                'max:100',
                Rule::unique('usuarios', 'email')->whereNull('deleted_at')->ignore($id),
            ],
            'current_password' => $request->filled('password') ? 'required|string' : 'nullable|string', // Requerir contraseña actual solo si se cambia la contraseña
            'password' => ['nullable', 'string', 'min:6', 'confirmed',
                'regex:/[A-Z]/', 'regex:/[a-z]/', 'regex:/[0-9]/', 'regex:/[@$!%*?&]/',
            ], 
            'telefono' => [
                'required', 
                'digits:10', 
                Rule::unique('usuarios', 'telefono')->ignore($id),
            ],
            'rol' => 'required|in:Administrador,Barbero,Cliente',
        ], [
            'nombre.required' => 'El campo nombre es obligatorio.',
            'nombre.min' => 'El nombre debe tener al menos 10 caracteres.',
            'nombre.max' => 'El nombre no debe exceder los 50 caracteres',
            'nombre.regex' => 'El nombre solo puede contener letras y espacios, sin números ni caracteres especiales.',

            'nombre_usuario.required' => 'El campo nombre de usuario es obligatorio.',
            'nombre_usuario.unique' => 'El nombre de usuario ya está en uso.',
            'nombre_usuario.min' => 'El nombre de usuario debe tener al menos 5 caracteres.',
            'nombre_usuario.max' => 'El nombre de usuario no debe exceder los 20 caracteres',

            'email.required' => 'El campo correo electrónico es obligatorio.',
            'email.unique' => 'El correo electrónico ya está registrado.',
            'email.max' => 'El correo no debe superar los 100 caracteres.',

            'current_password.required' => 'Debes ingresar la contraseña actual para cambiar la contraseña.',
            'password.required' => 'El campo contraseña es obligatorio.',
            'password.min' => 'La contraseña debe tener al menos 6 caracteres.',
            'password.regex' => 'La contraseña debe tener al menos una mayuscula, una minuscula, un numero y un caracter especial (@$!%*?&)',
            'password.confirmed' => 'Las contraseñas no coinciden.',

            'telefono.required' => 'El campo teléfono es obligatorio',
            'telefono.digits' => 'El teléfono deben ser 10 números',
            'telefono.unique' => 'El teléfono ya está registrado',

            'rol.required' => 'El campo rol es obligatorio.',
            'rol.in' => 'El rol seleccionado no es válido.',
        ]);

        // Verificar si el usuario está asociado a la tabla de barberos
        if ($usuario->rol === 'Barbero' && $request->rol !== 'Barbero') {
            if ($usuario->barberos()->exists()) {
                return redirect()->route('usuarios.edit', $usuario->id)
                    ->with('error', 'No puedes cambiar el rol del usuario porque está registrado como barbero.');
            }
        }

        // Si el usuario es un barbero, actualiza también en la tabla barberos
        if ($usuario->rol == 'Barbero' && $usuario->barberos()->exists()) {
            $usuario->barberos()->update([
                'nombre' => $request->nombre,
                'telefono' => $request->telefono,
            ]);
        }

        $usuario->nombre = $request->nombre;
        $usuario->nombre_usuario = $request->nombre_usuario;
        $usuario->email = $request->email;
        // Validar contraseña actual si se intenta cambiar la contraseña
        if ($request->filled('password')) {
            if (!Hash::check($request->current_password, $usuario->password)) {
                throw ValidationException::withMessages([
                    'current_password' => 'La contraseña actual no es correcta.',
                ]);
            }

            // Encriptar la nueva contraseña y asignarla
            $usuario->password = bcrypt($validated['password']);
        }
        $usuario->telefono = $request->telefono;
        $usuario->rol = $request->rol;
        $usuario->save();

        return redirect()->route('usuarios.index')->with('success', 'Usuario actualizado correctamente.');
    }




    // Funciones sin rol
    public function editSinRol($id)
    {
        $usuarioActual = Auth::user();
        if ($usuarioActual->rol !== 'Administrador' && $usuarioActual->id != $id) {
            abort(404, 'No tienes permiso para editar este usuario');
        }

        $usuario = Usuario::findOrFail($id);
        $usuarios = Usuario::all();
        return view('usuarios.edit_sinrol', compact('usuario', 'usuarios'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function updateSinRol(Request $request, $id)
    {
        $usuarioActual = Auth::user();
        if ($usuarioActual->rol !== 'Administrador' && $usuarioActual->id != $id) {
            abort(404, 'No tienes permiso para actualizar este usuario');
        }

        $usuario = Usuario::findOrFail($id);

        $validated = $request->validate([
            'nombre' => [
                'required',
                'string',
                'min:10',
                'max:50',
                'regex:/^[A-Za-zÀ-ÿÑñ\s]+$/'
            ],
            'nombre_usuario' => [
                'required',
                'min:5',
                'max:20',
                Rule::unique('usuarios', 'nombre_usuario')->whereNull('deleted_at')->ignore($id),
            ],
            'email' => [
                'required', 
                'email', 
                'max:100',
                Rule::unique('usuarios', 'email')->whereNull('deleted_at')->ignore($id),
            ],
            'current_password' => $request->filled('password') ? 'required|string' : 'nullable|string', // Requerir contraseña actual solo si se cambia la contraseña
            'password' => ['nullable', 'string', 'min:6', 'confirmed',
                'regex:/[A-Z]/', 'regex:/[a-z]/', 'regex:/[0-9]/', 'regex:/[@$!%*?&]/',
            ],
            'telefono' => [
                'required', 
                'digits:10', 
                Rule::unique('usuarios', 'telefono')->ignore($id),
            ], 
        ], [
            'nombre.required' => 'El campo nombre es obligatorio.',
            'nombre.min' => 'El nombre debe tener al menos 10 caracteres.',
            'nombre.max' => 'El nombre no debe exceder los 50 caracteres',
            'nombre.regex' => 'El nombre solo puede contener letras y espacios, sin números ni caracteres especiales.',

            'nombre_usuario.required' => 'El campo nombre de usuario es obligatorio.',
            'nombre_usuario.unique' => 'El nombre de usuario ya está en uso.',
            'nombre_usuario.min' => 'El nombre de usuario debe tener al menos 5 caracteres.',
            'nombre_usuario.max' => 'El nombre de usuario no debe exceder los 20 caracteres',

            'email.required' => 'El campo correo electrónico es obligatorio.',
            'email.unique' => 'El correo electrónico ya está registrado.',
            'email.max' => 'El correo no debe superar los 100 caracteres.',

            'current_password.required' => 'Debes ingresar la contraseña actual para cambiar la contraseña.',
            'password.required' => 'El campo contraseña es obligatorio.',
            'password.min' => 'La contraseña debe tener al menos 6 caracteres.',
            'password.regex' => 'La contraseña debe tener al menos una mayuscula, una minuscula, un numero y un caracter especial (@$!%*?&)',
            'password.confirmed' => 'Las contraseñas no coinciden.',

            'telefono.required' => 'El campo teléfono es obligatorio',
            'telefono.digits' => 'El teléfono deben ser 10 números',
            'telefono.unique' => 'El teléfono ya está registrado',
        ]);

        // Verificar si el usuario está asociado a la tabla de barberos
        if ($usuario->rol === 'Barbero' && $request->rol !== 'Barbero') {
            if ($usuario->barberos()->exists()) {
                return redirect()->route('usuarios.edit', $usuario->id)
                    ->with('error', 'No puedes cambiar el rol del usuario porque está registrado como barbero.');
            }
        }

        // Si el usuario es un barbero, actualiza también en la tabla barberos
        if ($usuario->rol == 'Barbero' && $usuario->barberos()->exists()) {
            $usuario->barberos()->update([
                'nombre' => $request->nombre,
                'telefono' => $request->telefono,
            ]);
        }

        $usuario->nombre = $request->nombre;
        $usuario->nombre_usuario = $request->nombre_usuario;
        $usuario->email = $request->email;
        // Validar contraseña actual si se intenta cambiar la contraseña
        if ($request->filled('password')) {
            if (!Hash::check($request->current_password, $usuario->password)) {
                throw ValidationException::withMessages([
                    'current_password' => 'La contraseña actual no es correcta.',
                ]);
            }

            // Encriptar la nueva contraseña y asignarla
            $usuario->password = bcrypt($validated['password']);
        }
        $usuario->telefono = $request->telefono;
        $usuario->save();

        return redirect('/')->with('success', 'Usuario actualizado correctamente.');
    }






    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $usuario = Usuario::findOrFail($id);
        // Verificar si el usuario tiene barberos asociados
        if ($usuario->barberos()->exists()) {
            return redirect()->route('usuarios.index')
                ->with('error', 'No se puede eliminar el usuario porque está asociado a uno o más barberos.');
        }
        $usuario->delete();

        return redirect()->route('usuarios.index')->with('success', 'Usuario eliminado correctamente.');
    }

    public function exportarPDF()
    {
        // Obtener usuario autenticado
        $usuario = Auth::user()->nombre;

        $lista = Usuario::all(); // Obtener todos los usuarios
        // $pdf = Pdf::loadView('usuarios.pdf', compact('lista'));
        // return $pdf->download('usuarios.pdf');
        $pdf = Pdf::loadView('usuarios.pdf', compact('lista', 'usuario'))
                ->setPaper('a4', 'portrait');
        return $pdf->stream('usuarios.pdf');
    }




    ################################################################################################
    ####           AQUI VAN TODAS LAS PETICIONES MEDIANTE API PARA LA APLICACION MOVIL          ####
    ################################################################################################

    /**
     * Autentificación para servicios web.
     */
    // public function login(Request $request)
    // {
    //     if ( Auth::attempt(['nombre_usuario' => $request->nu, 
    //     'password' => $request->p ]) ) {
    //         $usuario = Auth::user();
            
    //         //Generamos la llave de api's
    //         $token = Str::random(60);
    //         $request->user()->forceFill([
    //             'api_token' => hash('sha256', $token),
    //         ])->save();

    //         return json_encode(['respuesta' => 'Bienvenido',
    //                             'token' => $token,
    //                         'datos' => $usuario]);
    //     }
    //     return '{"respuesta":"Denegado"}';
    // }

    public function login(Request $request)
    {
    if (Auth::attempt(['nombre_usuario' => $request->nu, 'password' => $request->p])) {
        $usuario = Auth::user();

        // Verificar si el rol es "Administrador"
        if ($usuario->rol === 'Administrador') {
            return response()->json(['respuesta' => 'Acceso denegado para administradores'], 403);
        }

        // Generamos la llave de API
        $token = Str::random(60);
        $usuario->forceFill([
            'api_token' => hash('sha256', $token),
        ])->save();

        return response()->json([
            'respuesta' => 'Bienvenido',
            'token' => $token,
            'datos' => $usuario
        ], 200, [], JSON_UNESCAPED_UNICODE);        
        }
        return response()->json(['respuesta' => 'Denegado'], 401);
    }

    public function registrar(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nombre' => [
                'required',
                'string',
                'min:10',
                'max:50',
                'regex:/^[A-Za-zÀ-ÿÑñ\s]+$/'
            ],
            'nombre_usuario' => [
                'required',
                'min:5',
                'max:20',
                Rule::unique('usuarios', 'nombre_usuario')->whereNull('deleted_at')
            ],
            'email' => [
                'required', 
                'email', 
                'max:100', 
                Rule::unique('usuarios', 'email')->whereNull('deleted_at')
            ],
            'password' => [
                'required',
                'string',
                'min:6',
                'confirmed',
                'regex:/[A-Z]/',
                'regex:/[a-z]/',
                'regex:/[0-9]/',
                'regex:/[@$!%*?&]/'
            ],
            'telefono' => [
                'required', 
                'digits:10', 
                'unique:usuarios,telefono',
            ]
        ], [
            'nombre.required' => 'El campo nombre es obligatorio.',
            'nombre.min' => 'El nombre debe tener al menos 10 caracteres.',
            'nombre.max' => 'El nombre no debe exceder los 50 caracteres',
            'nombre.regex' => 'El nombre solo puede contener letras y espacios.',

            'nombre_usuario.required' => 'El campo nombre de usuario es obligatorio.',
            'nombre_usuario.unique' => 'El nombre de usuario ya está en uso.',
            'nombre_usuario.min' => 'El nombre de usuario debe tener al menos 5 caracteres.',
            'nombre_usuario.max' => 'El nombre de usuario no debe exceder los 20 caracteres.',

            'email.required' => 'El campo correo electrónico es obligatorio.',
            'email.unique' => 'El correo electrónico ya está registrado.',
            'email.max' => 'El correo no debe superar los 100 caracteres.',

            'password.required' => 'El campo contraseña es obligatorio.',
            'password.min' => 'La contraseña debe tener al menos 6 caracteres.',
            'password.regex' => 'La contraseña debe tener al menos una mayúscula, una minúscula, un número y un carácter especial (@$!%*?&).',
            'password.confirmed' => 'Las contraseñas no coinciden.',

            'telefono.required' => 'El campo teléfono es obligatorio',
            'telefono.digits' => 'El teléfono debe tener 10 dígitos',
            'telefono.unique' => 'El teléfono ya está registrado'
        ]);

        // Verificar si la validación falla
        if ($validator->fails()) {
            return response()->json([
                'respuesta' => 'Error',
                'errors' => $validator->errors()
            ], 422); // 422 Unprocessable Entity
        }

        // Crear el usuario con rol por defecto "Cliente"
        $usuario = new Usuario();
        $usuario->nombre = $request->nombre;
        $usuario->nombre_usuario = $request->nombre_usuario;
        $usuario->email = $request->email;
        $usuario->password = bcrypt($request->password);
        $usuario->telefono = $request->telefono;        
        $usuario->rol = 'Cliente'; // Asignar rol por defecto
        $usuario->save();

        return response()->json([
            'respuesta' => 'Registrado',
            'datos' => $usuario
        ], 201, [], JSON_UNESCAPED_UNICODE);
    }

}