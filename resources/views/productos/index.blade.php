@extends('layouts.base')

@section('contenido')
<link rel="stylesheet" href="{{ asset('css/inicio.css') }}">
<link rel="stylesheet" href="{{ asset('css/estilos.css') }}">

<!-- Sección de Productos -->
<section class="productos">
    <h1><strong>Productos Disponibles</strong></h1>

    <!-- Formulario de Búsqueda -->
    <form action="{{ route('productos.index') }}" method="GET" class="my-4">
        <div class="row justify-content-center align-items-center">
            <div class="col-12 col-md-6">
                <div class="input-group">
                    <input value="{{ $busqueda }}" type="text" class="form-control" name="busqueda" placeholder="Buscar por nombre o código de barras...">
                    <button class="btn btn-primary" type="submit"><i class="bi bi-search"></i></button>
                </div>
            </div>
        </div>
    </form>

    <!-- Botón para agregar producto -->
    <a href="{{ route('productos.create') }}" class="btn btn-primary mb-3">
        <i class="bi bi-plus-circle"></i> Agregar producto
    </a>

    <a href="{{ route('productos.exportarPDF') }}" class="btn btn-danger mb-3" target="_blank">
        <i class="bi bi-file-earmark-pdf"></i> Exportar a PDF
    </a> 

    <!-- Tabla de Productos -->
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Código de Barras</th>
                    <th>Descripción</th>
                    <th>Stock</th>
                    <th>Precio</th>
                    <th class="texto-tabla">Editar</th>
                    <th class="texto-tabla">Eliminar</th>
                    <th class="texto-tabla">Añadir Stock</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($lista as $prod)
                <tr>
                    <td>{{ $prod->id }}</td>
                    <td>{{ $prod->nombre }}</td>
                    <td>{{ $prod->codigo_barras }}</td>
                    <td>{{ $prod->descripcion }}</td>
                    <td>{{ $prod->stock }}</td>
                    <td>${{ $prod->precio }}</td>
                    <td class="texto-tabla">
                        <a href="{{ route('productos.edit', $prod->id) }}" class="btn btn-warning">
                            <i class="bi bi-pencil"></i>
                        </a>
                    </td>
                    <td class="texto-tabla">
                        <form id="borrar{{ $prod->id }}" style="display: inline" action="{{ route('productos.destroy', $prod->id) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <a href="#" class="btn btn-danger" onclick="borrar({{ $prod->id }}, '{{ $prod->nombre }}')">
                                <i class="bi bi-trash"></i>
                            </a>
                        </form>
                    </td>
                    <td class="texto-tabla">
                        <a href="{{ route('inventarios.index', ['producto_id' => $prod->id]) }}" class="btn btn-success">
                            <i class="bi bi-plus-circle"></i>
                        </a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</section>

<script type="text/javascript">
    function borrar(id, nombre) {
        var confirmar = confirm('¿Deseas borrar el producto ' + nombre + '?');
        if (confirmar) {
            document.getElementById('borrar' + id).submit();
        }
    }
</script>
@endsection