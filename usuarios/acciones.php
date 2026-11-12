<?php
require_once '../modelo/usuarios.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $accion = $_POST['accion'] ?? '';
    $nombre = $_POST['nombre'] ?? '';
    $apellido = $_POST['apellido'] ?? '';
    $sexo = $_POST['sexo'] ?? '';
    $cuil = $_POST['cuil'] ?? '';
    $ndoc = $_POST['ndoc'] ?? '';
    $fnacimiento = $_POST['fnacimiento'] ?? '';
    $oficio = $_POST['oficio'] ?? '';
    $telefono = $_POST['telefono'] ?? '';
    $email = $_POST['email'] ?? '';
    $domicilio = $_POST['domicilio'] ?? '';
    $codpostal = $_POST['codpostal'] ?? '';
    $localidad = $_POST['localidad'] ?? '';
    $imagen = $_POST['imagen'] ?? '';
    $id = $_POST['id'] ?? '';

    if ($accion === 'agregar') {
        Usuario::agregar($nombre, $apellido, $sexo, $cuil, $ndoc, $fnacimiento, $oficio, $telefono, $email, $domicilio, $codpostal, $localidad, $imagen);
        header('Location: ../panel_usuarios.php');
        exit;
    }

    if ($accion === 'editar') {
        Usuario::editar($id, $nombre, $apellido, $sexo, $cuil, $ndoc, $fnacimiento, $oficio, $telefono, $email, $domicilio, $codpostal, $localidad, $imagen);
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