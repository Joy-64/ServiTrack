<?php

require_once __DIR__ . "/conexion.php";

$correo= $_POST['correo'];
$contrasena=$_POST['contrasena'];

$sql = "SELECT * FROM usuarios WHERE correo = :correo";

$consulta = $conexion->prepare($sql);

$consulta->execute([
    "correo" => $correo
]);

$usuario = $consulta->fetch();

if ($usuario && password_verify($contrasena, $usuario["contrasena"])) {
    echo "Login correcto";
} else {
    echo "Correo o contraseña incorrectos";
}