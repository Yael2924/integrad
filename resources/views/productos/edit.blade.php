@extends('layouts.base')

@section('contenido')
<link rel="stylesheet" href="{{ asset('css/inicio.css') }}">

<!-- Vista para Editar un Producto -->
<section class="editar-producto">
    <h1><strong>Editar Producto</strong></h1>

    @if( sizeof($errors) > 0 )
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('productos.update', $prod->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="col-md-6 mb-md-2">
            <label for="nombre" class="form-label">Nombre del Producto</label>
            <input value="{{ old('nombre', $prod->nombre) }}" type="text" class="form-control" id="nombre" name="nombre" 
                placeholder="Ingrese el nombre del producto" 
                minlength="5" maxlength="50" 
                pattern=".{5,50}" 
                title="El nombre debe tener entre 5 y 50 caracteres." 
                required>
        </div>
        
        <div class="col-md-6 mb-md-2">
            <label for="descripcion" class="form-label">Descripción</label>
            <textarea class="form-control" id="descripcion" name="descripcion" rows="3" 
                placeholder="Ingrese una descripción del producto" 
                minlength="15" maxlength="100" 
                pattern=".{15,100}" 
                title="La descripción debe tener entre 15 y 100 caracteres." 
                required>{{ old('descripcion', $prod->descripcion) }}</textarea>
        </div>        
        
        <div class="row">
            <div class="col-md-3 mb-md-2">
                <label for="precio" class="form-label">Precio</label>
                <input value="{{ old('precio', $prod->precio) }}" type="number" step="0.01" class="form-control" id="precio" name="precio" 
                    placeholder="Ingrese el precio del producto" 
                    min="1.00" 
                    title="El precio debe ser igual o mayor a 1." 
                    required>
            </div>
            
            <div class="col-md-3 mb-md-2">
                <label for="stock" class="form-label">Stock</label>
                <input value="{{ old('stock', $prod->stock) }}" type="number" step="1" class="form-control" id="stock" name="stock" 
                    placeholder="Ingrese el stock disponible" 
                    min="1" 
                    title="El stock debe ser igual o mayor a 1." 
                    required readonly>
            </div>
        </div>
        
        
        <div class="col-md-6 mb-md-2">
            <label for="codigo_barras" class="form-label">Código de Barras</label>
            <input value="{{ old('codigo_barras', $prod->codigo_barras) }}" type="text" class="form-control" id="codigo_barras" name="codigo_barras" 
                placeholder="Ingrese el código de barras" 
                minlength="10" maxlength="15" 
                pattern="[0-9]{10,15}" 
                title="Debe contener entre 10 y 15 dígitos numéricos" 
                required>
        </div>        

        <button type="submit" class="btn btn-primary">Guardar Producto</button>
        <button type="reset" class="btn btn-warning">Limpiar Formulario</button>
        <a href="{{ route('productos.index') }}" class="btn btn-secondary">Cancelar</a>
    </form>
</section>
@endsection