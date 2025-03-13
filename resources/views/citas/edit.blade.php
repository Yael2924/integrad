@extends('layouts.base')

@section('contenido')
<link rel="stylesheet" href="{{ asset('css/inicio.css') }}">

<!-- Vista para Editar una Cita -->
<section class="editar-cita">
    <h1><strong>Editar Cita</strong></h1>

    @if ( sizeof($errors)>0 )
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('citas.update', $cita->id) }}" method="POST">
        @csrf
        @method('PUT')

        <!-- Cliente (no editable) -->
        <div class="row mt-2">
            <div class="col-md-4">
                <label for="usuario_id" class="form-label">Cliente</label>
                <select name="usuario_id" class="form-control" required disabled>
                    <option value="{{ $cita->usuario_id }}" selected>{{ $cita->usuario->nombre }}</option>
                </select>
                <input type="hidden" name="usuario_id" value="{{ $cita->usuario_id }}">
            </div>


            <!-- Barbero (editable) -->
            <div class="col-md-4">
                <label for="barbero_id">Barbero</label>
                <select name="barbero_id" id="barbero_id" class="form-control" required>
                    @foreach($barberos as $barbero)
                        <option value="{{ $barbero->id }}" 
                            {{ $cita->barbero_id == $barbero->id ? 'selected' : '' }}>
                            {{ $barbero->nombre }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

    
        <div class="row mt-2">
            <div class="col-md-4">
                <label for="fecha">Selecciona la fecha de la Cita</label>
                <input type="date" name="fecha" id="fecha" class="form-control" required value="{{ $cita->fecha }}" min="{{ $cita->fecha }}">
            </div>
        
            <div class="col-md-4">
                <label for="hora">Seleccione la hora de la cita</label>
                <select name="hora" id="hora" class="form-control" required>
                    <!-- Aquí precargamos la hora de la cita actual -->
                    <option value="{{ $cita->hora }}" selected>
                        {{ \Carbon\Carbon::parse($cita->hora)->format('g:i a') }}
                    </option>
                    <!-- Las demás opciones se cargarán aquí con JavaScript -->
                </select>
            </div>
        </div>

        <br><br>
    
        <!-- Botones -->
        <button type="submit" class="btn btn-primary">Guardar Cita</button>
        <button type="reset" class="btn btn-warning">Limpiar Formulario</button>
        <a href="{{ route('citas.index') }}" class="btn btn-secondary">Cancelar</a>
    </form>
</section>

<script>
    // Obtenemos las horas disponibles al cargar la página
    document.addEventListener('DOMContentLoaded', function() {
        let barberoId = document.getElementById('barbero_id').value;
        let fecha = document.getElementById('fecha').value;

        if (barberoId && fecha) {
            fetchHorasDisponibles(barberoId, fecha);
        }
    });

    document.getElementById('barbero_id').addEventListener('change', function() {
        let barberoId = this.value;
        let fecha = document.getElementById('fecha').value;

        if (barberoId && fecha) {
            fetchHorasDisponibles(barberoId, fecha);
        }
    });

    document.getElementById('fecha').addEventListener('change', function() {
        let barberoId = document.getElementById('barbero_id').value;
        let fecha = this.value;

        if (barberoId && fecha) {
            fetchHorasDisponibles(barberoId, fecha);
        }
    });

    function fetchHorasDisponibles(barberoId, fecha) {
        fetch(`/get-horas-disponibles?fecha=${fecha}&barbero_id=${barberoId}`)
            .then(response => response.json())
            .then(data => {
                let horaSelect = document.getElementById('hora');
                // Limpiar las opciones (excepto la hora precargada)
                horaSelect.innerHTML = '<option value="">Elige una de las horas disponibles</option>'; // Limpiar las opciones;

                data.forEach(horario => {
                    let option = document.createElement('option');
                    option.value = horario.hora;

                    // Convertir la hora a formato AM/PM
                    let [hours, minutes] = horario.hora.split(':');
                    let date = new Date();
                    date.setHours(parseInt(hours), parseInt(minutes), 0, 0); 

                    // Convertir la hora en formato 12 horas AM/PM
                    let horaFormateada = date.toLocaleString('es-MX', {
                        hour: 'numeric',
                        minute: 'numeric',
                        hour12: true
                    });

                    // Mostrar la hora en el formato AM/PM
                    option.textContent = horaFormateada;
                    horaSelect.appendChild(option);
                });
            })
            .catch(error => {
                console.error('Error fetching horas disponibles:', error);
            });
    }
</script>

@endsection
