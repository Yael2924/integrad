@extends('layouts.base')

@section('contenido')
<link rel="stylesheet" href="{{ asset('css/inicio.css') }}">

<!-- Vista para Editar un Nuevo Barbero -->
<section class="editar-barbero">
    <h1><strong>Editar Barbero</strong></h1>

    @if( sizeof($errors)>0 )
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="alert alert-info">
        <strong>Si deseas modificar el nombre o teléfono del barbero dirigete a la seccion de Usuarios</strong>
    </div>

    <form action="{{ route('barberos.update',$barbe->id) }}" method="POST">
        @csrf
        @method('PUT')
        <!-- Selección de Usuario -->
        <div class="col-md-6 mb-md-2">
            <label for="usuario_id" class="form-label">Usuario</label>
            <select name="usuario_id" id="usuario_id" class="form-control" required>
                @foreach ($usuarios as $usuario)
                    <option value="{{ $usuario->id }}" {{ $barbe->usuario_id == $usuario->id ? 'selected' : '' }}
                        data-nombre="{{ $usuario->nombre }}" data-telefono="{{ $usuario->telefono }}">
                        {{ $usuario->nombre_usuario }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="col-md-6 mb-md-2">
            <label for="nombre" class="form-label">Nombre</label>
            <input value="{{$barbe->nombre}}" type="text" class="form-control" id="nombre" name="nombre"
                placeholder="Ingrese el nombre completo del barbero" 
                minlength="10" maxlength="50" 
                pattern=".{10,50}" 
                title="El nombre debe tener entre 10 y 50 caracteres." 
                required readonly disabled>
                <input type="hidden" name="nombre" value="{{ $barbe->nombre }}">
        </div>

        <div class="row">
            <div class="col-md-3 mb-md-2 mx-md-1">
                <label for="telefono">Teléfono:</label>
                <input value="{{ $barbe->telefono }}" type="tel" class="form-control" id="telefono" name="telefono" 
                    placeholder="Ingrese el número de teléfono"
                    minlength="10"
                    maxlength="10" 
                    pattern="[0-9]{10}" 
                    title="El teléfono debe tener 10 dígitos."
                    required readonly disabled>
                    <input type="hidden" name="telefono" value="{{ $barbe->telefono }}">
            </div>         
        </div>

        <div class="col-md-3 mb-md-2">
            <label for="estado" class="form-label">Estado</label>
            <select name="estado" id="estado" class="form-control" required>
                <option value="">Seleccione el Estado del Barbero</option>
                <option value="activo" {{ old('estado', $barbe->estado) == 1 ? 'selected' : '' }}>Activo</option>
                <option value="inactivo" {{ old('estado', $barbe->estado) == 0 ? 'selected' : '' }}>Inactivo</option>
            </select>
        </div>
        
        <br>

        <button type="submit" class="btn btn-primary">Guardar Barbero</button>
        <button type="reset" class="btn btn-warning">Limpiar Formulario</button>
        <a href="{{ route('barberos.index') }}" class="btn btn-secondary">Cancelar</a>
    </form>

    <br>
    <br>
</section>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        var usuarioSelect = document.getElementById("usuario_id");
        var nombreInput = document.getElementById("nombre");
        var telefonoInput = document.getElementById("telefono");

        usuarioSelect.addEventListener("change", function() {
            var selectedOption = this.options[this.selectedIndex];
            var nombre = selectedOption.getAttribute("data-nombre");
            var telefono = selectedOption.getAttribute("data-telefono");

            nombreInput.value = nombre || "";
            telefonoInput.value = telefono || "";
        });
    });
</script>
@endsection