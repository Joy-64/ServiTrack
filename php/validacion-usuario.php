<?php

$conexion= mysqli_connect("localhost", "root", "", "servitrack", 3307);

$correo= $_POST['correo'];
$contrasena=$_POST['contrasena'];

$sql = "SELECT * FROM usuarios WHERE correo='$correo' AND contrasena='$contrasena'";

$resultado= mysqli_query($conexion,$sql);

if(mysqli_num_rows($resultado)>0){
    echo "Login correcto";
}else{
   echo "Correo o contraseña incorrectos";
}