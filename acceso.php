<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Acceso ServiTrack</title>
    <link rel="stylesheet" href="./css/style.css">
</head>
<body class="pagina-acceso">
    <section class="top">
        <img src="./img/logo2.0.png" width="200px" height="100px"  alt="" class="logo-acceso">
    </section>
    <section class="cuerpo-acceso">
        <form action="./php/validacion-usuario.php" method="POST">
        <h2>Iniciar sesión</h2>
        <label for="correo">Correo electrónico</label>
        <input type="text" id="correo" name="correo">
        <label for="contrasena">Contraseña</label>
       <div class="contenedor-contrasena">

    <input
        type="password"
        id="contrasena"
        name="contrasena"
    >

    <button
        type="button"
        id="btnContrasena"
        aria-label="Mostrar contraseña"
    >
        <img
            src="./img/ojo.png"
            width="20"
            height="20"
            alt=""
        >
    </button>

</div>
   <a href="./registro.php" class="link-registrase">
    ¿No tenés una cuenta? Registrate
</a>
        </form>
       
        <script src="./js/acceso.js"></script>
        
    </section>
</body>
</html> 