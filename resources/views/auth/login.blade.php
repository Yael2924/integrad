<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Moshan Premium Barber Shop</title>
    <style>
        /* Estilos generales */
        body {
            font-family: 'Arial', sans-serif;
            background-image: url('../img/fondo.jpg'); /* Ruta de la imagen de fondo */
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            color: #fff;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            position: relative;
        }

        /* Overlay oscuro para el fondo */
        body::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.6); /* Fondo oscuro semi-transparente */
            z-index: 1;
        }

        .login-container {
            background-color: rgba(42, 42, 42, 0.9); /* Fondo semi-transparente */
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.5);
            width: 90%;
            max-width: 400px;
            text-align: center;
            position: relative;
            z-index: 2;
            margin: 1rem; /* Margen adicional para móviles */
        }

        .login-container h1 {
            font-family: 'Georgia', serif;
            font-size: 2rem;
            margin-bottom: 1rem;
            color: #E6E0D0;
            text-transform: uppercase;
        }

        .logo {
            width: 100%; /* El logo ocupa el 100% del contenedor */
            max-width: 350px; /* Tamaño máximo del logo */
            height: auto;
            filter: brightness(0) invert(1); /* Convierte el logo a blanco */
            margin-bottom: 1.5rem;
        }

        .login-container label {
            display: block;
            font-size: 0.9rem;
            margin-bottom: 0.5rem;
            color: #E6E0D0;
            text-align: left;
        }

        .login-container input {
            width: calc(100% - 1.5rem); /* Ajuste para igualar el espacio en ambos lados */
            padding: 0.75rem;
            margin: 0 0 1rem 0; /* Eliminamos márgenes laterales */
            border: 1px solid #444;
            border-radius: 5px;
            background-color: rgba(51, 51, 51, 0.8); /* Fondo semi-transparente */
            color: #fff;
            font-size: 1rem;
        }

        .login-container input:focus {
            border-color: #E6E0D0;
            outline: none;
        }

        .login-container button {
            width: 100%;
            padding: 0.75rem;
            background-color: #E6E0D0;
            color: #1a1a1a;
            border: none;
            border-radius: 5px;
            font-size: 1rem;
            font-weight: bold;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .login-container button:hover {
            background-color: #d6c7b8;
        }

        .login-container .error-message {
            color: #ff4d4d;
            font-size: 0.9rem;
            margin-top: 0.5rem;
            text-align: left;
        }

        .login-container .forgot-password {
            margin-top: 1rem;
            font-size: 0.9rem;
            color: #E6E0D0;
            text-decoration: none;
            display: inline-block;
        }

        .login-container .forgot-password:hover {
            text-decoration: underline;
        }

        /* Estilos responsivos */
        @media (max-width: 480px) {
            .login-container {
                padding: 1rem; /* Reducir el padding en móviles */
                margin: 0.5rem; /* Reducir el margen en móviles */
            }

            .login-container h1 {
                font-size: 1.5rem; /* Tamaño de fuente más pequeño en móviles */
            }

            .logo {
                max-width: 250px; /* Tamaño del logo ajustado para móviles */
            }

            .login-container input {
                padding: 0.5rem;
                font-size: 0.9rem;
            }

            .login-container button {
                padding: 0.5rem;
                font-size: 0.9rem;
            }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h1>Moshan Premium Barber Shop</h1>
        <br>
        <img src="../img/logo_barberia_sin_fondo.png" alt="Logo" class="logo"> <!-- Ruta del logo -->
        <form method="POST" action="{{ route('login') }}">
            @csrf
            <!-- Nombre de Usuario -->
            <div>
                <label for="nombre_usuario">Usuario</label>
                <input id="nombre_usuario" type="text" name="nombre_usuario" value="{{ old('nombre_usuario') }}" required autofocus autocomplete="Nombre de usuario">
                @if($errors->has('nombre_usuario'))
                    <div class="error-message">{{ $errors->first('nombre_usuario') }}</div>
                @endif
            </div>

            <!-- Contraseña -->
            <div>
                <label for="password">Contraseña</label>
                <input id="password" type="password" name="password" required autocomplete="current-password">
                @if($errors->has('password'))
                    <div class="error-message">{{ $errors->first('password') }}</div>
                @endif
            </div>

            <br>
            <!-- Botón de Iniciar Sesión -->
            <div>
                <button type="submit">Iniciar Sesión</button>
            </div>

            <!-- Enlace para recuperar contraseña -->
            {{-- <a href="#" class="forgot-password">¿Olvidaste tu contraseña?</a> --}}
        </form>
    </div>
</body>
</html>