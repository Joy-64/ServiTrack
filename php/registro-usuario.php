<?php

require_once __DIR__ . "/conexion.php";

$correo = $_POST['correo'];
$contrasena = $_POST['contrasena'];
$confirmarContrasena = $_POST["confirmarContrasena"];
$rol = "usuario";
if ($contrasena !== $confirmarContrasena) {
    exit("Las contraseñas no coinciden");
}
$contrasenaHasheada = password_hash($contrasena, PASSWORD_DEFAULT);

$sql = "INSERT INTO usuarios (correo, contrasena, rol)
        VALUES (:correo, :contrasena, :rol)";

$consulta = $conexion->prepare($sql);

$consulta->execute([
    "correo" => $correo,
    "contrasena" => $contrasenaHasheada,
    "rol" => $rol
]);

echo "Usuario registrado";