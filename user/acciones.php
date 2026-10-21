<?php
require_once '../modelo/usuarios.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $accion = $_POST['accion'] ?? '';
    $nombre = $_POST['nombre'] ?? '';
    $oficio = $_POST['oficio'] ?? '';
    $telefono = $_POST['telefono'] ?? '';
    $email = $_POST['email'] ?? '';
    $imagen = $_POST['imagen'] ?? '';
    $id = $_POST['id'] ?? '';

    if ($accion === 'agregar') {
        Usuario::agregar($nombre, $oficio, $telefono, $email, $imagen);
        header('Location: ../panel_usuarios.php');
        exit;
    }

    if ($accion === 'editar') {
        Usuario::editar($id, $nombre, $oficio, $telefono, $email, $imagen);
        header('Location: ../panel_usuarios.php');
        exit;
    }

    if ($accion === 'eliminar') {
        Usuario::eliminar($id);
        header('Location: ../panel_usuarios.php');
        exit;
    }
}
?>