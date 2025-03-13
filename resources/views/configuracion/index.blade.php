@extends('layouts.base')

@section('contenido')
<link rel="stylesheet" href="{{ asset('css/inicio.css') }}">
<link rel="stylesheet" href="{{ asset('css/estilos.css') }}">

<section>
    <h1><strong>Configuración</strong></h1>

    <br>

    <!-- Formulario para actualizar el porcentaje de ganancia -->
    <form action="{{ route('configuracion.guardar') }}" method="POST">
        @csrf
        <div class="form-group">
            <label for="porcentaje">Porcentaje de Ganancia de los Barberos:</label>
            <input type="number" id="porcentaje" name="porcentaje" class="form-control" value="{{ old('porcentaje', $configuracion->porcentaje_ganancia ?? 0) }}" min="0" max="100" required>
            @error('porcentaje')
                <div class="alert alert-danger mt-2">{{ $message }}</div>
            @enderror
        </div>

        <br>

        <button type="submit" class="btn btn-primary">Guardar Configuración</button>
    </form>
</section>
@endsection
