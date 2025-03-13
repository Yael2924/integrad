@extends('layouts.base')

@section('contenido')
<link rel="stylesheet" href="{{ asset('css/inicio.css') }}">
<link rel="stylesheet" href="{{ asset('css/estilos.css') }}">

<!-- Sección de Servicios -->
<section class="ventas">
    <h1><strong>Ventas de Servicios</strong></h1>

    <br>
    <form action="{{ route('ventas_servicios.index') }}" method="GET">
        <div class="row justify-content-center align-items-center">
            <div class="col-12 col-md-6">  <!-- Cambié para que sea responsivo sin afectar el texto -->
                <div class="input-group">
                    <input value="{{ $busqueda }}" type="text" class="form-control" name="busqueda" placeholder="Buscar venta de servicio por folio...">
                    <button class="btn btn-primary" type="submit"><i class="bi bi-search"></i></button>
                </div>
            </div>
        </div>
    </form>

    <br>
    

    @if (Auth::check() && Auth::user()->rol === 'Barbero')
    <!-- Botón para crear un nuevo servicio -->
    <a href="{{ route('ventas_servicios.create') }}" class="btn btn-primary mb-3">
        <i class="bi bi-plus-circle"></i> Agregar Venta de Servicios
    </a>
    @endif

    @if (Auth::check() && Auth::user()->rol === 'Administrador')
    <!-- Botón para sacar porcentaje de cada Barbero -->
    <a href="{{ route('ventas_servicios.porcentaje') }}" class="btn btn-warning mb-3">
        <i class="bi bi-file-earmark-text"></i> Calcular Ganancia del Barbero
    </a>
    @endif

    @if (Auth::check() && Auth::user()->rol === 'Administrador')
    <!-- Botón para el resumen de ventas de servicios por filtro -->
    <a href="{{ route('ventas_servicios.filtro') }}" class="btn btn-info mb-3">
        <i class="bi bi-file-earmark-text"></i> Reportes Por Filtro
    </a>
    @endif



    <br><br>
    
    <!-- Tabla de Servicios con responsividad -->
    <div class="table-responsive">  <!-- Agregado para hacer la tabla responsiva sin cambiar el tamaño del texto -->
        <table class="table">
            <thead>
                <tr>
                    <th class="texto-tablaNO">Folio</th>
                    <th class="texto-tablaNO">Fecha</th>
                    <th class="texto-tablaNO">Servicio</th>
                    <th class="texto-tablaNO">Barbero</th>
                    <th class="texto-tablaNO">Cantidad</th>
                    <th class="texto-tablaNO">Precio Unitario</th>
                    <th class="texto-tablaNO">Total</th>
                    <th class="texto-tablaNO">Exportar Recibo</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($lista as $venser)
                <tr>
                    <td class="texto-tablaNO">{{ $venser->id }}</td>
                    <td class="texto-tablaNO">{{ \Carbon\Carbon::parse($venser->fecha_hora)->format('d/m/Y H:i:s') }}</td>
                    <td class="texto-tablaNO">{{ $venser->servicio->nombre }}</td>
                    <td class="texto-tablaNO">{{ $venser->barbero->nombre }}</td>
                    <td class="texto-tablaNO">{{ $venser->cantidad }}</td>
                    <td class="texto-tablaNO">${{ $venser->precio_unitario }}</td>
                    <td class="texto-tablaNO">${{ $venser->total }}</td>
                    <td>
                        <a href="{{ route('ventas_servicios.pdf', $venser->id) }}" target="_blank" class="btn btn-danger">
                            <i class="bi bi-file-earmark-pdf"></i> Recibo
                        </a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        
    </div>
<!-- Agregar paginación debajo de la tabla -->
<div class="paginacion-container d-flex justify-content-center">
    {{ $lista->links() }}
</div>


</section>

@endsection
