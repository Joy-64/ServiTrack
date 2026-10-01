<?php require_once 'modelo/usuarios.php'; ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de usuarios</title>
        <link rel="icon" href="./img/logo3.0.jpeg">
        <link rel="stylesheet" href="./css/style.css">
    <!--LINKS para que funcione el mapa-->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
        integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.css" />


</head>

<body>
    <!--ACA esta la cabecera-->
    <header>
        <div class="header-logo">
            <img src="./img/logo2.0.png" width="200px" height="100px" alt="Logo ServiTrack">
        </div>
        <button id="menu-btn"><img src="./img/menu.png" width="20px" height="20px" alt="menu"></button>
        <nav class="header-nav" id="menu">
            <ul>
                <li><a class="items-menu" href="">¿Quiénes somos?</a></li>
                <li><a class="items-menu" href="">Nuestro trabajo</a></li>
                <li><a class="items-menu" href="">Demo</a></li>
                <li><a class="items-menu" href="">Contacto</a></li>
            </ul>
        </nav>
        <div class="header-boton"><button class="boton"><a class="login" href="">Acceso</a></button></div>
    </header>
    <!--ACA TERMINA-->

    <!--ACA empieza el Panel de usuarios-->

     <div class="container mt-5">
        <div class="row mb-4">
            <div class="col">
                <h1>Usuarios</h1>
            </div>
            <div class="col text-end">
                <a href="usuarios/vistas/editar.php" class="btn btn-success">+ Agregar</a>
            </div>
        </div>

        <table class="table table-striped table-hover">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Oficio</th>
                    <th>Teléfono</th>
                    <th>Email</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php $usuarios = Usuario::obtenerTodas(); foreach ($usuarios as $u): ?>
                <tr>
                    <td><?php echo $u->Id; ?></td>
                    <td><?php echo $u->Nombre; ?></td>
                    <td><?php echo $u->Oficio; ?></td>
                    <td><?php echo $u->Telefono; ?></td>
                    <td><?php echo $u->Email; ?></td>
                    <td>
                        <a href="usuarios/vistas/ver.php?id=<?php echo $u->Id; ?>" class="btn btn-sm btn-primary">Ver</a>
                        <a href="usuarios/vistas/editar.php?id=<?php echo $u->Id; ?>" class="btn btn-sm btn-warning">Editar</a>
                        <form method="POST" action="usuarios/acciones.php" style="display:inline;">
                            <input type="hidden" name="accion" value="eliminar">
                            <input type="hidden" name="id" value="<?php echo $u->Id; ?>">
                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('¿Está seguro?')">Eliminar</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>


    <!--ACA comienza el footer, que incluye logo sin texto, links como en el header y boton de descarga de app-->

    <footer class="footer">
        <div class="footer-logo">
            <img src="./img/logo 2.1.png" width="100px" height="100px" alt="Logo ServiTrack">
        </div>

        <nav class="foot-enlace">
            <a href="index.html">Inicio</a>
            <a href="servicios.html">Servicios</a>
            <a href="quienes-somos.html">Quienes somos</a>
            <a href="contacto.html">Contacto</a>
        </nav>

        <p class="derechos">&copy; @año | ServiTrack. Todos los derechos reservados.</p>

    </footer>
     <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>


</html>