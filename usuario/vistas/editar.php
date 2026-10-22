<?php require_once '../../modelo/usuarios.php'; 
                    $id = $_GET['id'] ?? null;
$usuario = $id ? Usuario::obtenerPorId($id) : null;
$titulo = $usuario ? 'Editar Usuario' : 'Agregar Usuario';
$accion = $usuario ? 'editar' : 'agregar';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $titulo; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container mt-5">
        <div class="row">
            <div class="col-md-6">
                <h1><?php echo $titulo; ?></h1>

                <form method="POST" action="../acciones.php">
                    <input type="hidden" name="accion" value="<?php echo $accion; ?>">
                    <?php if ($usuario): ?>
                    <input type="hidden" name="id" value="<?php echo $usuario->Id; ?>">
                    <?php endif; ?>
                    <div class="mb-3">
                        <label class="form-label">Nombre</label>
                        <input type="text" class="form-control" name="nombre" value="<?php echo $usuario ? $usuario->Nombre : ''; ?>" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Correo</label>
                        <input type="email" class="form-control" name="email" value="<?php echo $usuario ? $usuario->Email : ''; ?>" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Oficio</label>
                        <input type="text" class="form-control" name="oficio" value="<?php echo $usuario ? $usuario->Oficio : ''; ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Teléfono</label>
                        <input type="text" class="form-control" name="telefono" value="<?php echo $usuario ? $usuario->Telefono : ''; ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Imagen</label>
                        <input type="text" class="form-control" name="imagen" value="<?php echo $usuario ? $usuario->Imagen : ''; ?>" required>
                    </div>
                    <div class="d-grid gap-2 d-sm-flex">
                        <button type="submit" class="btn btn-primary">Guardar</button>
                        <a href="../../panel_usuarios.php" class="btn btn-secondary">Cancelar</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
