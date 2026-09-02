<?php

$host = "localhost";
$puerto = "3307";
$baseDeDatos = "servitrack";
$usuario = "root";
$contrasena = "";

$dsn = "mysql:host=$host;port=$puerto;dbname=$baseDeDatos;charset=utf8mb4";

try {
    $conexion = new PDO($dsn, $usuario, $contrasena);

    $conexion->setAttribute(
        PDO::ATTR_ERRMODE,
        PDO::ERRMODE_EXCEPTION
    );

    echo "Conexión exitosa";
} catch (PDOException $error) {
    echo "Error de conexión: " . $error->getMessage();
}