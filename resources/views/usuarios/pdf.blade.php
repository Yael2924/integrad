<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Usuarios</title>
    <style>
        /* Estilos generales */
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            color: black;
            text-align: center;
        }

        /* Contenedor del logo alineado a la izquierda */
        .logo-container {
            position: absolute; /* Para fijarlo en la parte superior */
            top: 10px;  /* Ajusta la distancia desde arriba */
            left: 10px; /* Ajusta la distancia desde la izquierda */
        }

        .logo-container img {
            max-width: 230px; /* Ajusta el tamaño según necesites */
            height: auto;
        }

        h1 {
            color: black;
            margin-bottom: 20px;
            font-size: 24px;
        }

        /* Estilos de la tabla */
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 0 auto;
        }

        th, td {
            border: 1px solid #ddd;
            padding: 12px;
            text-align: left;
        }

        th {
            background-color: black;
            color: white;
            font-weight: bold;
        }

        tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        tr:hover {
            background-color: #f1f1f1;
        }

        /* Footer */
        footer {
            position: absolute;
            bottom: 0;
            width: 100%;
            text-align: center;
            font-size: 12px;
            color: #4A4A4A;
            /* background-color: #f9f9f9; */
            padding: 10px 0;
        }
    </style>
</head>
<body>

    <!-- Espacio para el logo alineado a la izquierda -->
    <div class="logo-container">
        <img src="img/logo_barberia_sin_fondo.png" alt="Logo de la empresa">
    </div>
    
    <br>
    <br>
    
    <h1>Usuarios</h1>

    <table>
        <thead>
            <tr>
                <th>Nombre Completo</th>
                <th>Usuario</th>
                <th>Email</th>
                <th>Telefono</th>
                <th>Rol</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($lista as $usuario)
            <tr>
                <td>{{ $usuario->nombre }}</td>
                <td>{{ $usuario->nombre_usuario }}</td>
                <td>{{ $usuario->email }}</td>
                <td>{{ $usuario->telefono }}</td>
                <td>{{ ucfirst ($usuario->rol) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <footer>
        <p>Generado el {{ now()->format('d/m/Y') }} por {{ $usuario }}</p>
    </footer>

</body>
</html>