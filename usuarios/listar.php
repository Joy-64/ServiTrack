<?php require_once '../modelo/usuarios.php'; ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Usuarios</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <div class="row mb-4">
            <div class="col">
                <h1>Usuarios</h1>
            </div>
            <div class="col text-end">
                <a href="editar.php" class="btn btn-success">+ Agregar</a>
            </div>
        </div>

        <table class="table table-striped table-hover">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Oficio</th>
                    <th>Telefono</th>
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
                        <a href="ver.php?id=<?php echo $u->Id; ?>" class="btn btn-sm btn-primary">Ver</a>
                        <a href="editar.php?id=<?php echo $u->Id; ?>" class="btn btn-sm btn-warning">Editar</a>
                        <form method="POST" action="../acciones.php" style="display:inline;">
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<!--Comentario de prueba-->