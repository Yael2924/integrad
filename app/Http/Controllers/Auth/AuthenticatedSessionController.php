<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(Request $request)
    {
    $request->validate([
        'nombre_usuario' => ['required', 'string'],
        'password' => ['required', 'string'],
    ]);

    if (!auth()->attempt($request->only('nombre_usuario', 'password'))) {
        return back()->withErrors([
            'nombre_usuario' => 'Las credenciales no son válidas.',
        ]);
    }

    $user = Auth::user();

    if ($user->rol === 'Cliente') {
        Auth::logout();
        // return redirect('/login')->withErrors(['Error'=> 'No tienes permiso para acceder a la plataforma']);
        return redirect('/login')->withErrors(['nombre_usuario'=> 'No tienes permiso para acceder a la plataforma']);
    }

    $barberoExiste = \App\Models\Barbero::where('usuario_id', $user->id)->exists();

    if ($user->rol === "Barbero" && !$barberoExiste) {  
        Auth::logout(); // Cierra sesión inmediatamente
        return redirect('/login')->withErrors(['nombre_usuario' => 'No puedes iniciar sesión hasta que tu cuenta esté registrada como barbero.'
        ]);
    }

    $request->session()->regenerate();

    return redirect()->intended(route('inicio'));

    }


    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}