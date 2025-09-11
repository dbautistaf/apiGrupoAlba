<!DOCTYPE html>
<html>

<head>
    <title>¡Bienvenido a {{ $afiliado->obrasocial->locatorio }}!</title>
</head>

<body>
    {{-- <h1>Mensaje desde Laravel</h1> --}}
    <h2>Hola {{ $afiliado->nombre . ' ' . $afiliado->apellidos }}</h2>
    <h3>¡Te damos la bienvenida a {{ $afiliado->obrasocial->locatorio }}!</h3>
    <p>Ya podés ingresar a nuestra plataforma digital para gestionar tus trámites de salud de forma ágil, segura y
        personalizada.</p>
    <br>
    <p>Desde ahora, vas a poder:</p>
    <ul>
        <li><i class="pi pi-check"></i>Consultar y actualizar tus datos</li>
        <li><i class="pi pi-check"></i>Descargar tu credencial digital</li>
        <li><i class="pi pi-check"></i>Acceder a la cartilla de prestadores</li>
        <li><i class="pi pi-check"></i>Realizar autorizaciones, solicitudes y seguimientos online.</li>
    </ul>
    <br>
    <p>Tu cuenta ya está habilitada y podés ingresar desde cualquier dispositivo en:</p>
    <p><b>https://grupoalbasalud.oridheansoft.com</b></p>
    <p>Usuario: {{ $afiliado->dni }}</p>
    <p>Contraseña: {{ $afiliado->dni }}</p>
    <br>
    <p>Te recomendamos cambiar tu contraseña en el primer ingreso y mantener tus datos actualizados para una mejor
        atención.</p>
    <p>Si necesitás ayuda o tenés alguna duda, nuestro equipo de soporte está para asistirte:</p>
    <p>Email: <b>soporte@oridhean.com</b></p>
    <p>Whatsapp: <b>+54 911 5113-7385</b></p>
    <br>
    <p>Gracias por confiar en nosotros.</p>
    <p>Saludos cordiales.</p>
    <h2>{{ $afiliado->obrasocial->locatorio }}</h2>
</body>

</html>
