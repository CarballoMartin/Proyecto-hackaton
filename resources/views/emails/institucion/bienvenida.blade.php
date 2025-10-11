<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bienvenido/a</title>
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
        }

        .header {
            font-size: 24px;
            font-weight: bold;
            text-align: center;
            margin-bottom: 20px;
        }

        .content p {
            margin-bottom: 15px;
        }

        .credentials {
            background-color: #f7f7f7;
            padding: 15px;
            border-radius: 5px;
            margin:
                20px 0;
        }

        .credentials strong {
            display: block;
            margin-bottom: 5px;
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
            ¡Bienvenida, Institución {{ $user->name }}!
        </div>
        <div class="content">
            <p>Hola,</p>
            <p>Nos complace informarte que tu institución ha sido registrada exitosamente en
                nuestra plataforma.</p>
            <p>Hemos creado una cuenta de usuario para que puedas acceder y gestionar la
                información correspondiente. A continuación, encontrarás tus credenciales de acceso. Te
                recomendamos encarecidamente cambiar tu contraseña después de iniciar sesión por primera vez.</p>

            <div class="credentials">
                <strong>Usuario:</strong>
                <span>{{ $user->email }}</span>
                <br><br>
                <strong>Contraseña Temporal:</strong>
                <span>{{ $passwordTemporal }}</span>
            </div>

            <p>Puedes iniciar sesión en el siguiente enlace:</p>
            <p><a href="{{ route('login') }}">Iniciar Sesión</a></p>

            <p>Si tienes alguna pregunta, no dudes en contactarnos.</p>
            <p>Saludos cordiales,<br>El equipo de la plataforma.</p>
        </div>
        <div class="footer">
            Este es un correo electrónico generado automáticamente. Por favor, no respondas a
            este mensaje.
        </div>
    </div>
</body>

</html>