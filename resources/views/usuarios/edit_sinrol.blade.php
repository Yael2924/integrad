@extends('layouts.base')

@section('contenido')
<link rel="stylesheet" href="{{ asset('css/inicio.css') }}">

<!-- Vista para Editar un Usuario -->
<section class="editar-usuario">
    <h1><strong>Editar Usuario</strong></h1>

    @if( sizeof($errors)>0 )
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('usuarios.update_sinrol',$usuario->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="row">
            <div class="col-md-4 mb-md-2">
                <label for="nombre" class="form-label">Nombre Completo</label>
                <input type="text" name="nombre" id="nombre" class="form-control" value="{{ old('nombre', $usuario->nombre) }}" 
                    placeholder="Ingrese el nombre completo del barbero" 
                    minlength="10" maxlength="50" 
                    pattern=".{10,50}" 
                    title="El nombre debe tener entre 10 y 50 caracteres." 
                    required>
            </div>
    
            <div class="col-md-4 mb-md-2">
                <label for="nombre_usuario" class="form-label">Nombre de Usuario</label>
                <input type="text" name="nombre_usuario" id="nombre_usuario" class="form-control" value="{{ old('nombre_usuario', $usuario->nombre_usuario) }}" 
                    placeholder="Ingrese el nombre de usuario" 
                    minlength="5" maxlength="20" 
                    pattern=".{5,20}" 
                    title="El nombre debe tener entre 5 y 20 caracteres." 
                    required>
            </div>
    
            <div class="col-md-4 mb-md-2">
                <label for="email" class="form-label">Correo Electrónico</label>
                <input type="email" name="email" id="email" class="form-control" value="{{ old('email', $usuario->email) }}" required>
            </div>       
    
            <div class="col-md-4 mb-md-2">
                <label for="current_password" class="form-label">Contraseña Actual</label>
                <input type="password" name="current_password" id="current_password" class="form-control">
                <small class="text-muted">Introduce tu contraseña actual para confirmar el cambio.</small>
                @error('current_password')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>
    
            <div class="col-md-4 mb-md-2">
                <label for="password" class="form-label">Nueva Contraseña (opcional)</label>
                <input type="password" name="password" id="password" class="form-control">
            </div>
    
            <div class="col-md-4 mb-md-2">
                <label for="password_confirmation" class="form-label">Confirmar Contraseña</label>
                <input type="password" name="password_confirmation" id="password_confirmation" class="form-control">
            </div>
    
            <div class="col-md-4 mb-md-4">
                <label for="telefono" class="form-label">Teléfono</label>
                <input type="tel" name="telefono" id="telefono" class="form-control" value="{{ old('telefono', $usuario->telefono) }}" 
                placeholder="Ingrese el número de teléfono"
                    minlength="10"
                    maxlength="10" 
                    pattern="[0-9]{10}" 
                    title="El teléfono debe tener 10 dígitos."
                    required>
            </div>
    
            <br>

            <!-- Campo Rol -->
            <input type="hidden" name="rol" value="{{ $usuario->rol }}">
        </div>
        

        <button type="submit" class="btn btn-primary">Guardar Usuario</button>
        <button type="reset" class="btn btn-warning">Limpiar Formulario</button>
        <a href="{{ url()->previous() }}" class="btn btn-secondary">Cancelar</a>
        <br>
        <br>
    </form>
</section>
@endsection