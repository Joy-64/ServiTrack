<?php

$conexion = mysqli_connect("localhost", "root", "", "servitrack",3307);

$correo = $_POST['correo'];
$contrasena = $_POST['contrasena'];
$rol = "usuario";


$sql = "INSERT INTO usuarios(correo,contrasena,rol) VALUES('$correo', '$contrasena', '$rol')";

mysqli_query($conexion, $sql);

echo 'Usuario registrado';