@extends('layouts.base')

@section('contenido')
<link rel="stylesheet" href="{{ asset('css/inicio.css') }}">

<!-- Vista para Crear una Nueva Cita -->
<section class="crear-cita">
    <h1><strong>Agendar Cita</strong></h1>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('citas.store') }}" method="POST">
        @csrf

        <!-- Cliente -->
        <div class="row mt-2">
            <div class="col-md-4">
                <label for="usuario_id" class="form-label">Cliente</label>
                <select name="usuario_id" class="form-control" required>
                    <option value="">Seleccione el Cliente</option>
                    @foreach($clientes as $cliente)
                        <option value="{{ $cliente->id }}" {{ old('usuario_id') == $cliente->id ? 'selected' : '' }}>
                            {{ $cliente->nombre }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-4">
                <label for="barbero_id">Barbero</label>
                <select name="barbero_id" id="barbero_id" class="form-control" required>
                    <option value="">Seleccione un barbero</option>
                    @foreach($barberos as $barbero)
                        <option value="{{ $barbero->id }}">{{ $barbero->nombre }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    
        <div class="row mt-2">
            <div class="col-md-4">
                <label for="fecha">Selecciona la fecha de la Cita</label>
                <input type="date" name="fecha" id="fecha" class="form-control" required min="{{ date('Y-m-d') }}">
            </div>
        
            <div class="col-md-4">
                <label for="hora">Seleccione la hora de la cita</label>
                <select name="hora" id="hora" class="form-control" required>
                    <option value="">Elige una de las horas disponibles</option>
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
    // Obtener las horas disponibles cuando se seleccione el barbero y la fecha
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
                horaSelect.innerHTML = '<option value="">Elige una de las horas disponibles</option>'; // Limpiar las opciones

                data.forEach(horario => {
                    let option = document.createElement('option');
                    option.value = horario.hora;

                    // Convertir la hora a formato AM/PM (ajustando la zona horaria)
                    let [hours, minutes] = horario.hora.split(':');
                    let date = new Date();
                    date.setHours(parseInt(hours), parseInt(minutes), 0, 0); // Usar la hora y minuto directamente

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

    //         fetch(`/get-horas-disponibles?fecha=${fecha}&barbero_id=${barberoId}`)
    // .then(response => response.json())
    // .then(data => {
    //     console.log(data); // Verifica la estructura de los datos
    //     if (Array.isArray(data)) {
    //         data.forEach(item => {
    //             console.log(item);
    //         });
    //     } else {
    //         console.error('La respuesta no es un arreglo:', data);
    //     }
    // })
    // .catch(error => {
    //     console.error('Error fetching horas disponibles:', error);
    // });


    
</script>

@endsection