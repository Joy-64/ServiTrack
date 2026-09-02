<?php
$name = $_POST['name'];
$email = $_POST['email'];
$tel = $_POST['tel'];
$message = $_POST['message'];

$conexion = mysqli_connect("localhost", "root", "", "servitrack") or die("Error al conectar a la base de datos");
$consulta = "INSERT INTO contactanos (nombre, correo_electronico, telefono, mensaje) VALUES ('$name', '$email', '$tel', '$message')";

mysqli_query($conexion, $consulta) or die("Error al insertar los datos en la base de datos");
echo "Mensaje enviado exitosamente."
?>