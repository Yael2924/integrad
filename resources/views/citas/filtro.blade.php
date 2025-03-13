@extends('layouts.base')

@section('contenido')
<link rel="stylesheet" href="{{ asset('css/inicio.css') }}">
<link rel="stylesheet" href="{{ asset('css/estilos.css') }}">

<section class="reporte">
    <h1><strong>Reporte de Citas por Filtro</strong></h1>

    <form action="{{ route('citas.filtro') }}" method="GET">
        <div class="row">
            <div class="col-md-4">
                <label for="fecha_inicio">Fecha Inicio:</label>
                <input type="date" id="fecha_inicio" name="fecha_inicio" class="form-control" 
                    value="{{ request('fecha_inicio') }}" required>
            </div>

            <div class="col-md-4">
                <label for="fecha_fin">Fecha Fin:</label>
                <input type="date" id="fecha_fin" name="fecha_fin" class="form-control" 
                    value="{{ request('fecha_fin') }}" required min="{{ request('fecha_inicio') }}">
            </div>

            <div class="col-md-4">
                <label for="usuario_id">Cliente:</label>
                <select id="usuario_id" name="usuario_id" class="form-control">
                    <option value="">Todos</option>
                    @foreach ($usuarios as $cliente)
                        <option value="{{ $cliente->id }}" @selected(request('usuario_id') == $cliente->id)
                            @if($cliente->trashed()) style="color: red;" @endif>
                            {{ $cliente->nombre }}
                            @if($cliente->trashed()) (Eliminado) @endif
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="row mt-2">
            <div class="col-md-4">
                <label for="barbero_id">Barbero:</label>
                <select id="barbero_id" name="barbero_id" class="form-control">
                    <option value="">Todos</option>
                    @foreach ($barberos as $barbero)
                        <option value="{{ $barbero->id }}" 
                            @selected(request('barbero_id') == $barbero->id) 
                            @if($barbero->trashed()) style="color: red;" @endif>
                            {{ $barbero->nombre }} 
                            @if($barbero->trashed()) (Despedido) @endif
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-4">
                <label for="estado">Estado:</label>
                <select id="estado" name="estado" class="form-control">
                    <option value="">Todos</option>
                    <option value="Completado" {{ old('estado') == 'Completado' ? 'selected' : '' }}>Completado</option>
                    <option value="Pendiente" {{ old('estado') == 'Pendiente' ? 'selected' : '' }}>Pendiente</option>
                </select>
            </div>
        </div>
        
        <br>


        <button type="submit" class="btn btn-primary w-auto">Generar Reporte</button>

        <a href="{{ route('citas.index') }}" class="btn btn-secondary w-auto">Volver</a>

    </form>

    @if (isset($citas) && count($citas) > 0 && request('fecha_inicio') && request('fecha_fin'))
        <form action="{{ route('citas.exportarPDF') }}" method="POST" target="_blank">
            @csrf
            <input type="hidden" name="fecha_inicio" value="{{ request('fecha_inicio') }}">
            <input type="hidden" name="fecha_fin" value="{{ request('fecha_fin') }}">
            <input type="hidden" name="usuario_id" value="{{ request('usuario_id') }}">
            <input type="hidden" name="barbero_id" value="{{ request('barbero_id') }}">
            <input type="hidden" name="estado" value="{{ request('estado') }}">

            <button type="submit" class="btn btn-danger mt-3">
                <i class="bi bi-file-earmark-pdf"></i> Exportar a PDF
            </button>
        </form>

        <h2 class="mt-4">Resultados</h2>
        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Fecha</th>
                    <th>Cliente</th>
                    <th>Barbero</th>
                    <th>Estado</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($citas as $cita)
                <tr>
                    <td>{{ $cita->id }}</td>
                    <td>{{ $cita->fecha }}</td>
                    <td>{{ $cita->usuario->nombre }}</td>
                    <td>{{ $cita->barbero->nombre }}</td>
                    <td>{{ $cita->estado }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Agregar paginación debajo de la tabla -->
        <div class="paginacion-container d-flex justify-content-center">
            {{ $citas->appends(request()->query())->links() }}
        </div>

        {{-- <h3>Total de Citas: ${{ $totalVentas }}</h3> --}}
    
    @else
        <div class="alert alert-warning mt-3">
            No se encontraron citas en el rango seleccionado.
        </div>
    @endif
</section>
@endsection
