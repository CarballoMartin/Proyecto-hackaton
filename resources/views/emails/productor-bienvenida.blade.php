<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bienvenido/a al Sistema de Gestión</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
        }
        .container {
            width: 90%;
            max-width: 600px;
            margin: 20px auto;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .header {
            font-size: 24px;
            font-weight: bold;
            text-align: center;
            margin-bottom: 20px;
            color: #2c3e50;
        }
        .content p {
            margin-bottom: 15px;
        }
        .highlight {
            background-color: #f7f7f7;
            padding: 20px;
            border-radius: 5px;
            margin: 20px 0;
            font-size: 16px;
            text-align: center;
        }
        .button-container {
            text-align: center;
            margin: 30px 0;
        }
        .button {
            background-color: #3498db;
            color: #ffffff !important; /* Asegurar color de texto */
            padding: 15px 25px;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
        }
        .footer {
            text-align: center;
            font-size: 12px;
            color: #777;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            ¡Bienvenido, {{ $user->name }}!
        </div>
        <div class="content">
            <p>Hola {{ $user->name }},</p>
            <p>Nos complace informarte que un administrador te ha registrado exitosamente en el <strong>Sistema de Gestión Ovino-Caprino</strong>.</p>
            <p>Para acceder, no necesitas una contraseña. Simplemente utiliza tu correo electrónico o tu número de teléfono para iniciar sesión. El sistema te enviará un código de acceso único cada vez que quieras ingresar.</p>

            <div class="highlight">
                <p>Tu identificador de usuario puede ser:</p>
                <strong>Tu Email:</strong> {{ $user->email }}<br>
                @if($user->productor && $user->productor->telefono)
                    <strong>o tu Teléfono:</strong> {{ $user->productor->telefono }}
                @endif
            </div>

            <div class="button-container">
                <a href="http://127.0.0.1:8000/" class="button">Iniciar Sesión en la Plataforma</a>
            </div>

            <p>Si tienes alguna pregunta, no dudes en contactar al administrador que gestionó tu alta.</p>
            <p>Saludos cordiales,<br>El equipo del Sistema de Gestión.</p>
        </div>
        <div class="footer">
            Este es un correo electrónico generado automáticamente. Por favor, no respondas a este mensaje.
        </div>
    </div>
</body>
</html>
