<?php

$conexion = mysqli_connect("localhost", "root", "", "servitrack",3307);

$correo = $_POST['correo'];
$contrasena = $_POST['contrasena'];
$rol = "usuario";


$sql = "INSERT INTO usuarios(coreo,contrasena) VALUES('$correo', '$contrasena', 'rol';

mysqli_query($conexion, $sql);

echo "Usuario registrado";