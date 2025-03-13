@extends('layouts.base')

@section('contenido')
<link rel="stylesheet" href="{{ asset('css/inicio.css') }}">

<!-- Vista para Crear un Nuevo Registro de Inventario -->
<section class="crear-inventario">
    <h1><strong>Surtir Producto: {{ $producto->nombre ?? 'Selecciona un producto' }}</strong></h1>

    @if( sizeof($errors) > 0 )
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('inventarios.store') }}" method="POST">
        @csrf
        <br>

        <!-- Campo oculto para enviar el ID del producto sin necesidad de seleccionarlo -->
        <input type="hidden" name="producto_id" value="{{ $producto->id }}">

        <div class="col-md-3">
            <label for="fecha">Selecciona la fecha que esta surtiendo</label>
            <input type="date" name="fecha" id="fecha" class="form-control" required>
        </div>

        <br>
            
        <div class="col-md-3 mb-md-2">
            <label for="stock" class="form-label">Stock</label>
            <input type="number" class="form-control" id="stock" name="stock" 
                value="{{ old('stock') }}" 
                placeholder="Ingrese el stock a surtir" 
                min="1" 
                title="El stock debe ser igual o mayor a 1." 
                required>
        </div>

                
        <br>

        <button type="submit" class="btn btn-primary">Guardar Inventario</button>
        <button type="reset" class="btn btn-warning">Limpiar Formulario</button>
        <a href="{{ route('inventarios.index', ['producto_id' => $producto_id]) }}" class="btn btn-secondary">Cancelar</a>
    </form>
    <br>
    <br>
</section>
@endsection