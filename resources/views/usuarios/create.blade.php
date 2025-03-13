@extends('layouts.base')

@section('contenido')
<link rel="stylesheet" href="{{ asset('css/inicio.css') }}">

<!-- Vista para Crear un Nuevo Usuario -->
<section class="crear-usuario">
    <h1><strong>Nuevo Usuario</strong></h1>

    @if( sizeof($errors)>0 )
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('usuarios.store') }}" method="POST">
        @csrf
        <div class="row">
            <div class="col-md-4 mb-md-2">
                <label for="nombre" class="form-label">Nombre Completo</label>
                <input type="text" class="form-control" id="nombre" name="nombre" value="{{ old('nombre') }}" 
                    placeholder="Ingrese el nombre completo del barbero" 
                    minlength="10" maxlength="50" 
                    pattern=".{10,50}" 
                    title="El nombre debe tener entre 10 y 50 caracteres." 
                    required>
            </div>
    
            <div class="col-md-4 mb-md-2">
                <label for="nombre_usuario" class="form-label">Nombre de Usuario</label>
                <input type="text" class="form-control" id="nombre_usuario" name="nombre_usuario" value="{{ old('nombre_usuario') }}" 
                    placeholder="Ingrese el nombre de usuario" 
                    minlength="5" maxlength="20" 
                    pattern=".{5,20}" 
                    title="El nombre debe tener entre 5 y 20 caracteres." 
                    required>
            </div>
    
            <div class="col-md-4 mb-md-2">
                <label for="email" class="form-label">Correo Electrónico</label>
                <input type="email" name="email" id="email" class="form-control" 
                placeholder="Ingrese el correo electronico" value="{{ old('email') }}" required maxlength="100">
            </div>

            <div class="col-md-4 mb-md-2">
                <label for="password" class="form-label">Contraseña</label>
                <input type="password" name="password" id="password" class="form-control"  
                placeholder="Ingrese una contraseña segura" aria-describedby="passwordHelp" required>
            </div>
    
            <div class="col-md-4 mb-md-2">
                <label for="password_confirmation" class="form-label">Confirmar Contraseña</label>
                <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" 
                placeholder="Confirme la contraseña" aria-describedby="password_confirmationHelp" required>
            </div>

            <div class="col-md-4 mb-md-2">
                <label for="telefono" class="form-label">Teléfono</label>
                <input type="tel" name="telefono" id="telefono" class="form-control" 
                placeholder="Ingrese el número de teléfono" value="{{ old('telefono') }}"
                    minlength="10"
                    maxlength="10" 
                    pattern="[0-9]{10}" 
                    title="El teléfono debe tener 10 dígitos."
                    required>
            </div>
    
            <div class="col-md-4 mb-md-4">
                <label for="rol" class="form-label">Rol</label>
                <select name="rol" id="rol" class="form-control" required>
                    <option value="">Selecciona un rol</option>
                    <option value="Administrador" {{ old('rol') == 'Administrador' ? 'selected' : '' }}>Administrador</option>
                    <option value="Barbero" {{ old('rol') == 'Barbero' ? 'selected' : '' }}>Barbero</option>
                    <option value="Cliente" {{ old('rol') == 'Cliente' ? 'selected' : '' }}>Cliente</option>
                </select>
            </div>
        </div>
        
        <br>

        <button type="submit" class="btn btn-primary">Guardar Usuario</button>
        <button type="reset" class="btn btn-warning">Limpiar Formulario</button>
        <a href="{{ route('usuarios.index') }}" class="btn btn-secondary">Cancelar</a>

        <br>
        <br>
    </form>
</section>
@endsection