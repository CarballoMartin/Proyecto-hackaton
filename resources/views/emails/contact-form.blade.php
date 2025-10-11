<!DOCTYPE html>
<html>
<head>
    <title>Nuevo Mensaje de Contacto</title>
</head>
<body>
    <h1>Nuevo Mensaje de Contacto</h1>
    <p>Has recibido un nuevo mensaje a través del formulario de contacto del sitio web.</p>
    <ul>
        <li><strong>Nombre:</strong> {{ $data['name'] }}</li>
        <li><strong>Email:</strong> {{ $data['email'] }}</li>
        @if(!empty($data['phone']))
            <li><strong>Teléfono:</strong> {{ $data['phone'] }}</li>
        @endif
        <li><strong>Asunto:</strong> {{ $data['subject'] }}</li>
    </ul>
    <hr>
    <h2>Mensaje:</h2>
    <p>{{ $data['message'] }}</p>
</body>
</html>