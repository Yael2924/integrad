@extends('layouts.base')

@section('contenido')
<link rel="stylesheet" href="{{ asset('css/inicio.css') }}">
<link rel="stylesheet" href="{{ asset('css/estilos.css') }}">

<!-- Sección de Barberos -->
<section class="barberos">
    <h1><strong>Barberos Contratados</strong></h1>

    <br>
    <form action="{{ route('barberos.index') }}" method="GET">
        <div class="row justify-content-center align-items-center">
            <div class="col-12 col-md-6">  <!-- Cambié para que sea responsivo sin afectar el texto -->
                <div class="input-group">
                    <input value="{{ $busqueda }}" type="text" class="form-control" name="busqueda" placeholder="Buscar barberos...">
                    <button class="btn btn-primary" type="submit"><i class="bi bi-search"></i></button>
                </div>
            </div>
        </div>
    </form>
    <br>
    
    <!-- Botón para registrar un nuevo barbero -->
    <a href="{{ route('barberos.create') }}" class="btn btn-primary mb-3">
        <i class="bi bi-plus-circle"></i> Agregar Barbero
    </a> 
    
    <a href="{{ route('barberos.exportarPDF') }}" class="btn btn-danger mb-3" target="_blank">
        <i class="bi bi-file-earmark-pdf"></i> Exportar a PDF
    </a>

    <br><br>
    
    <!-- Tabla de Barberos con responsividad -->
    <div class="table-responsive">  <!-- Agregado para hacer la tabla responsiva sin cambiar el tamaño del texto -->
        <table class="table">
            <thead>
                <tr>
                    <th class="texto-tabla1">ID</th>
                    <th class="texto-tabla1">Usuario</th>
                    <th class="texto-tabla1">Nombre del Barbero</th>
                    <th class="texto-tabla1">Teléfono</th>
                    <th class="texto-tabla1">Estado</th>
                    <th class="texto-tabla">Editar</th>
                    <th class="texto-tabla">Eliminar</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($lista as $barbe)
                <tr>
                    <td class="texto-tabla1">{{ $barbe->id }}</td>
                    <td class="texto-tabla1">{{ $barbe->usuario->nombre_usuario ?? 'N/A' }}</td>
                    <td class="texto-tabla1">{{ $barbe->nombre }}</td>
                    <td class="texto-tabla1">{{ $barbe->telefono }}</td>
                    <td class="texto-tabla1">{{ $barbe->estado == 1 ? 'Activo' : 'Inactivo'}}</td>
                    <td class="texto-tabla">
                        <!-- Botón de editar con ícono -->
                        <a href="{{ route('barberos.edit', $barbe->id) }}" class="btn btn-warning">
                            <i class="bi bi-pencil"></i>  <!-- Icono de editar -->
                        </a>
                    </td>
                    <td class="texto-tabla">
                        <!-- Botón de eliminar con ícono -->
                        <form id="borrar{{$barbe->id}}" style="display: inline" action="{{ route('barberos.destroy', $barbe->id) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <a href="#" class="btn btn-danger" onclick="borrar({{ $barbe->id }}, '{{ $barbe->nombre }}')">
                                <i class="bi bi-trash"></i>  <!-- Icono de eliminar -->
                            </a>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        
    </div>
</section>

<script type="text/javascript">
    function borrar(id, nombre) {
        var confirmar = confirm('¿Deseas borrar el barbero ' + nombre +'?');
        if( confirmar ) {
            var formulario = document.getElementById('borrar'+id);
            formulario.submit();
        }
    }
</script>
@endsection
