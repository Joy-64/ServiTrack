<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
     <section class="top">
        <img src="./img/logo2.0.png" width="200px" height="100px"  alt="" class="logo-acceso">
    </section>
    <section class="cuerpo-acceso">
        <form action="./php/registro-usuario.php" method="POST">
        <h2>Registrarse</h2>
        <label for="correo">Correo electrónico</label>
        <input type="text" id="correo" name="correo">
        <label for="contrasena">Contraseña</label>
        <input type="password" id="contrasena" name="contrasena">
        <button type="button" id="btnContrasena" ><img src="./img/ojo.png" width="20px" height="20px" alt=""></button>
         <button type="submit">Registrarse</button>

        </form>
       
        <script src="./js/acceso.js"></script>
        
</body>
</html>