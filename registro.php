<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro ServiTrack</title>
    <link rel="stylesheet" href="./css/style.css">
</head>

<body class="pagina-acceso">

    <section class="top">
        <img
            src="./img/logo2.0.png"
            width="200"
            height="100"
            alt="Logo de ServiTrack"
            class="logo-acceso"
        >
    </section>

    <section class="cuerpo-acceso">

        <form action="./php/registro-usuario.php" method="POST">

            <h2>Crear una cuenta</h2>

            <label for="correo">Correo electrónico</label>
            <input
                type="email"
                id="correo"
                name="correo"
                required
            >

            <label for="contrasena">Contraseña</label>
            <input
                type="password"
                id="contrasena"
                name="contrasena"
                required
            >

            <label for="confirmarContrasena">Repetir contraseña</label>
            <input
                type="password"
                id="confirmarContrasena"
                name="confirmarContrasena"
                required
            >

            <button type="submit">Crear cuenta</button>

            <a href="./acceso.php" class="link-registrase">
                ¿Ya tenés una cuenta? Iniciá sesión
            </a>

        </form>

    </section>

</body>

</html>