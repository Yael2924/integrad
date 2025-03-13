@extends('layouts.base')

@section('contenido')
<link rel="stylesheet" href="{{ asset('css/inicio.css') }}">
<link rel="stylesheet" href="{{ asset('css/estilos.css') }}">

<!-- Sección de Citas -->
<section class="citas">
    <h1><strong>Gestión de Citas</strong></h1>

    @if(session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif


    <br>
    {{-- <form action="{{ route('citas.index') }}" method="GET">
        <div class="row justify-content-center align-items-center">
            <div class="col-12 col-md-6">  <!-- Cambié para que sea responsivo sin afectar el texto -->
                <div class="input-group">
                    <input value="{{ $busqueda }}" type="text" class="form-control" name="busqueda" placeholder="Buscar citas...">
                    <button class="btn btn-primary" type="submit"><i class="bi bi-search"></i></button>
                </div>
            </div>
        </div>
    </form> --}}
    <br>
    
    <!-- Botón para crear una nueva cita -->
    <a href="{{ route('citas.create') }}" class="btn btn-primary mb-3">
        <i class="bi bi-plus-circle"></i> Agregar Cita
    </a>    

    <!-- Botón para el resumen de citas por filtro -->
    <a href="{{ route('citas.filtro') }}" class="btn btn-danger mb-3">
        <i class="bi bi-file-earmark-text"></i> Citas Por Filtro
    </a>

    {{--<a href="{{ route('citas.exportarPDF') }}" class="btn btn-danger mb-3" target="_blank">
        <i class="bi bi-file-earmark-pdf"></i> Exportar a PDF
    </a>--}}

    <br><br>
    
    <!-- Tabla de Citas con responsividad -->
    <div class="table-responsive">  <!-- Agregado para hacer la tabla responsiva sin cambiar el tamaño del texto -->
        <table class="table">
            <thead>
                <tr>
                    <th class="texto-tabla1">ID</th>
                    <th class="texto-tabla1">Fecha</th>
                    <th class="texto-tabla1">Hora</th>
                    <th class="texto-tabla1">Cliente</th>
                    <th class="texto-tabla1">Barbero</th>
                    <th class="texto-tabla1">Estado</th>
                    <th class="texto-tabla">Reagendar</th>
                    <th class="texto-tabla">Eliminar</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($citas as $cita)
                <tr>
                    <td class="texto-tabla1">{{ $cita->id }}</td>
                    <td class="texto-tabla1">{{ \Carbon\Carbon::parse($cita->fecha)->format('d/m/Y') }}</td>
                    <td class="texto-tabla1">{{ \Carbon\Carbon::parse($cita->hora)->format('g:i a') }}</td>
                    <td class="texto-tabla1">{{ $cita->usuario->nombre }}</td>
                    <td class="texto-tabla1">{{ $cita->barbero->nombre }}</td>
                    <td class="texto-tabla1">{{ $cita->estado }}</td>
                    <td class="texto-tabla">
                        @if ($cita->estado === 'Completado')
                        <button class="btn btn-warning" disabled>
                            <i class="bi bi-pencil"></i> Cita completada
                        </button>
                        @else
                            <!-- Botón de editar con ícono -->
                            <a href="{{ route('citas.edit', $cita->id) }}" class="btn btn-warning">
                                <i class="bi bi-pencil"></i>  <!-- Icono de editar -->
                            </a>
                        @endif          
                    </td>
                    <td class="texto-tabla">
                        @if ($cita->estado === 'Pendiente')
                            <button class="btn btn-danger" disabled>
                                <i class="bi bi-trash"></i> Cita pendiente
                            </button>
                        @else
                            <form id="borrar{{ $cita->id }}" style="display: inline" action="{{ route('citas.destroy', $cita->id) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <a href="#" class="btn btn-danger" onclick="borrar({{ $cita->id }}, '{{ $cita->usuario->nombre }}', 
                                    '{{ \Carbon\Carbon::parse($cita->fecha)->format('d/m/Y') }}', 
                                    '{{ \Carbon\Carbon::parse($cita->hora)->format('g:i a') }}', 
                                    '{{ $cita->barbero->nombre }}')">
                                    <i class="bi bi-trash"></i>
                                </a>
                                
                            </form>
                        @endif
                        
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        
    </div>
    <!-- Agregar paginación debajo de la tabla -->
    <div class="paginacion-container d-flex justify-content-center">
        {{ $fechas->links() }}
    </div>
</section>

<script type="text/javascript">
    function borrar(id, nombreCliente, fecha, hora, nombreBarbero) {
        var confirmar = confirm('¿Deseas borrar la cita de ' + nombreCliente +
                                ' con fecha ' + fecha + ' a las ' + hora + 
                                ' con el barbero ' + nombreBarbero + '?');
        if( confirmar ) {
            var formulario = document.getElementById('borrar'+id);
            formulario.submit();
        }
    }
</script>

@endsection  