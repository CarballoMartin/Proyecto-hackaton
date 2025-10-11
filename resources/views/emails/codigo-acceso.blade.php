<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tu código de acceso</title>
</head>
<body style="font-family: Arial, sans-serif; color: #333;">
    <div style="max-width: 600px; margin: auto; padding: 20px; border: 1px solid #ddd; border-radius: 10px;">
        <h1 style="color: #0056b3;">Tu Código de Acceso</h1>
        <p>Hola,</p>
        <p>Usa el siguiente código para completar tu inicio de sesión. Este código es válido por 10 minutos.</p>
        <div style="text-align: center; margin: 20px 0;">
            <span style="display: inline-block; font-size: 24px; font-weight: bold; padding: 10px 20px; background-color: #f2f2f2; border-radius: 5px; letter-spacing: 3px;">
                {{ $codigo }}
            </span>
        </div>
        <p>Si no has solicitado este código, puedes ignorar este correo de forma segura.</p>
        <hr style="border: none; border-top: 1px solid #ddd; margin: 20px 0;">
        <p style="font-size: 12px; color: #888;">Gracias,<br>El equipo de {{ config('app.name') }}</p>
    </div>
</body>
</html>
