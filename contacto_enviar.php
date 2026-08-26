<?php
$name = $_POST['name'];
$email = $_POST['email'];
$tel = $_POST['tel'];
$message = $_POST['message'];

mysqli_connect("localhost", "root", "", "servitrack") or die("Error al conectar a la base de datos");


?>