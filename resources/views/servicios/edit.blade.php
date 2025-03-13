@extends('layouts.base')

@section('contenido')
<link rel="stylesheet" href="{{ asset('css/inicio.css') }}">

<!-- Vista para Crear un Nuevo Servicio -->
<section class="crear-servicio">
    <h1><strong>Editar Servicio</strong></h1>

    @if( sizeof($errors)>0 )
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('servicios.update',$servi->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="row">
            <div class="col-md-4 mb-md-2">
                <label for="nombre" class="form-label">Nombre del Servicio</label>
                <input value="{{ old('nombre', $servi->nombre) }}" type="text" class="form-control" id="nombre" name="nombre" 
                    placeholder="Ingrese el nombre del servicio" 
                    minlength="5" maxlength="50" 
                    pattern=".{5,50}" 
                    title="El nombre debe tener entre 5 y 50 caracteres." 
                    required>
            </div>

            <div class="col-md-3 mb-md-2">
                <label for="precio" class="form-label">Precio</label>
                <input value="{{ old('precio', $servi->precio) }}" type="number" step="0.01" class="form-control" id="precio" name="precio" 
                    placeholder="Ingrese el precio del servicio" 
                    min="20.00" 
                    title="El precio debe ser igual o mayor a 20." 
                    required>
            </div>
    
            {{-- <div class="col-md-3 mb-md-2">
                <label for="disponibilidad" class="form-label">Disponibilidad</label>
                <select class="form-control" name="disponibilidad" id="disponibilidad" aria-describedby="disponibilidadHelp" required>
                    <option value="1" {{ $servi->disponibilidad == 1 ? 'selected' : '' }}>Disponible</option>
                    <option value="0" {{ $servi->disponibilidad == 0 ? 'selected' : '' }}>No Disponible</option>
                </select>
            </div>         --}}
        </div>
        
    
        <div class="col-md-7 mb-md-2">
            <label for="descripcion" class="form-label">Descripción</label>
            <textarea class="form-control" id="descripcion" name="descripcion" rows="3" 
                placeholder="Ingrese una descripción del producto" 
                minlength="15" maxlength="100" 
                pattern=".{15,100}" 
                title="La descripción debe tener entre 15 y 100 caracteres." 
                required>{{ old('descripcion', $servi->descripcion) }}</textarea>
        </div>        
    
        <div class="mb-3">
            <label for="duracion" class="form-label">Duración</label>
            <input value="{{ old('duracion', $servi->duracion) }}" type="number" step="1" class="form-control" id="duracion" name="duracion" 
                placeholder="Ingrese la duración del servicio en minutos" 
                min="10"
                max="180" 
                title="La duración debe ser entre 10 y 180" 
                required>
        </div>
    

        <button type="submit" class="btn btn-primary">Guardar Servicio</button>
        <button type="reset" class="btn btn-warning">Limpiar Formulario</button>
        <a href="{{ route('servicios.index') }}" class="btn btn-secondary">Cancelar</a>
    </form>
</section>
@endsection
