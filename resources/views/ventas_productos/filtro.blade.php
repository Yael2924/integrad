@extends('layouts.base')

@section('contenido')
<link rel="stylesheet" href="{{ asset('css/inicio.css') }}">
<link rel="stylesheet" href="{{ asset('css/estilos.css') }}">

<section class="reporte">
    <h1><strong>Reporte de Ventas de Productos por Filtro</strong></h1>

    <form action="{{ route('ventas_productos.filtro') }}" method="GET">
        <div class="form-group">
            <label for="fecha_inicio">Fecha Inicio:</label>
            <input type="date" id="fecha_inicio" name="fecha_inicio" class="form-control" required>
        </div>

        <div class="form-group">
            <label for="fecha_fin">Fecha Fin:</label>
            <input type="date" id="fecha_fin" name="fecha_fin" class="form-control" required>
        </div>

        <br>

        <button type="submit" class="btn btn-primary">Generar Reporte</button>
        <a href="{{ route('ventas_productos.index') }}" class="btn btn-secondary">Volver</a>
    </form>

    @if (isset($ventas) && count($ventas) > 0 && request('fecha_inicio') && request('fecha_fin'))
        <form action="{{ route('ventas_productos.exportarPDF') }}" method="POST" target="_blank">
            @csrf
            <input type="hidden" name="fecha_inicio" value="{{ request('fecha_inicio') }}">
            <input type="hidden" name="fecha_fin" value="{{ request('fecha_fin') }}">
            
            <button type="submit" class="btn btn-danger mt-3">
                <i class="bi bi-file-earmark-pdf"></i> Exportar a PDF
            </button>
        </form>
    @endif


    @if (isset($ventas) && count($ventas) > 0 && request('fecha_inicio') && request('fecha_fin'))
        <h2 class="mt-4">Resultados</h2>
        <table class="table">
            <thead>
                <tr>
                    <th>Folio</th>
                    <th>Fecha</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($ventas as $venta)
                <tr>
                    <td>{{ $venta->id }}</td>
                    <td>{{ $venta->fecha_hora }}</td>
                    <td>${{ $venta->total }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <h3>Total de Ventas: ${{ $totalVentas }}</h3>
    
    @else
        <div class="alert alert-warning mt-3">
            No se encontraron ventas en el rango seleccionado.
        </div>
    @endif
</section>

@endsection
