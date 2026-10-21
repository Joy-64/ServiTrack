<?php require_once '../modelo/usuarios.php'; 
$id = $_GET['id'] ?? null;
$usuario = $id ? Usuario::obtenerPorId($id) : null;
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ver Usuario</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <div class="row">
            <div class="col-md-6">
                <h1>Detalle de Usuario</h1>

                <?php if ($usuario): ?>
                <div class="card">
                    <div class="card-body">
                        <p><strong>ID:</strong> <?php echo $usuario->Id; ?></p>
                        <p><strong>Nombre:</strong> <?php echo $usuario->Nombre; ?></p>
                        <p><strong>Oficio:</strong> <?php echo $usuario->Oficio; ?></p>
                        <p><strong>Telefono:</strong> <?php echo $usuario->Telefono; ?></p>
                        <p><strong>Email:</strong> <?php echo $usuario->Email; ?></p>
                        <p><img src="<?php echo $usuario->Imagen; ?>" width="50" height="50" alt="Imagen"></p>
                    </div>
                </div>

                <div class="mt-3">
                    <a href="../../panel_usuarios.php" class="btn btn-secondary">Volver</a>
                    <a href="editar.php?id=<?php echo $usuario->Id; ?>" class="btn btn-warning">Editar</a>
                </div>
                <?php else: ?>
                <div class="alert alert-danger">Usuario no encontrado</div>
                <a href="../../panel_usuarios.php" class="btn btn-secondary">Volver</a>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<!--Comentario de prueba-->