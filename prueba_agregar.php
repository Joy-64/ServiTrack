<?php
require_once 'modelo/usuarios.php';

$id = Usuario::agregar([
    'Nombre' => 'Prueba', 'Apellido' => 'Uno', 'Sexo' => 'Masculino', 'CUIL' => '20123456789', 'NDoc' => '12345678', 'IdTdoc' => '2', 'IdUsuarios' => '14',
    'FNacimiento' => '1990-05-20', 'Domicilio' => '', 'CodPostal' => '', 'Localidad' => '', 'Oficio' => '', 'Telefono' => '', 'Email' => '', 'Imagen' => '', 'Oficios' => ['Plomero', 'Gasista', 'Mecánico']
]);
var_dump($id);