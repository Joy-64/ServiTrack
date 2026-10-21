<?php
// datos_ejemplo.php
// Datos de EJEMPLO compartidos por panel.php y trabajos.php.
// Mientras no haya base de datos, los dos archivos leen la lista de acá,
// así siempre muestran lo mismo. Con la base, esta lista se reemplaza por una consulta.
//
// $trabajos es un arreglo (lista). Cada trabajo es otro arreglo con claves:
//   estado:  0 = en curso, 1 = terminado
//   cobrado: 0 = no cobrado, 1 = cobrado
// Van ordenados del más viejo al más nuevo.

// Arranca la SESIÓN: un espacio de memoria que PHP guarda para cada visitante
// mientras el navegador siga abierto. Tiene que ir antes de cualquier HTML.
session_start();

$trabajos = [
    ["id" => 1, "cliente" => "Pedro Díaz",      "fecha" => "2026-09-05", "descripcion" => "Cambio de tomacorrientes",      "horas" => 3,   "precio_hora" => 5000, "materiales" => 8000,  "estado" => 1, "cobrado" => 1],
    ["id" => 2, "cliente" => "Carlos Ruiz",     "fecha" => "2026-09-10", "descripcion" => "Instalación de termotanque",    "horas" => 5,   "precio_hora" => 5500, "materiales" => 45000, "estado" => 1, "cobrado" => 1],
    ["id" => 3, "cliente" => "Marta Gómez",     "fecha" => "2026-09-18", "descripcion" => "Cambio de cañería en cocina",   "horas" => 6,   "precio_hora" => 5000, "materiales" => 22000, "estado" => 0, "cobrado" => 0],
    ["id" => 4, "cliente" => "Pedro Díaz",      "fecha" => "2026-09-25", "descripcion" => "Instalación eléctrica del living", "horas" => 4.5, "precio_hora" => 5000, "materiales" => 15000, "estado" => 1, "cobrado" => 1],
    ["id" => 5, "cliente" => "Lucía Fernández", "fecha" => "2026-10-02", "descripcion" => "Pintura de dos habitaciones",   "horas" => 8,   "precio_hora" => 4500, "materiales" => 30000, "estado" => 0, "cobrado" => 0],
    ["id" => 6, "cliente" => "Carlos Ruiz",     "fecha" => "2026-10-08", "descripcion" => "Revisión de gas",               "horas" => 2,   "precio_hora" => 5500, "materiales" => 0,     "estado" => 1, "cobrado" => 0],
    ["id" => 7, "cliente" => "Ana Sosa",        "fecha" => "2026-10-13", "descripcion" => "Arreglo de cerradura",          "horas" => 1,   "precio_hora" => 5000, "materiales" => 6000,  "estado" => 0, "cobrado" => 0],
    ["id" => 8, "cliente" => "Marta Gómez",     "fecha" => "2026-10-16", "descripcion" => "Colocación de estantes",        "horas" => 3.5, "precio_hora" => 4500, "materiales" => 12000, "estado" => 1, "cobrado" => 0],
];

// ---- Cambios hechos con los botones "Modificar" y "Cobro" ----
// Sin base de datos, los cambios se recuerdan en la sesión (se pierden al cerrar el navegador).
// $_SESSION["cambios"] es una lista: ID del trabajo => datos que cambiaron.
if (isset($_SESSION["cambios"])) {
    foreach ($trabajos as $posicion => $t) {
        if (isset($_SESSION["cambios"][$t["id"]])) {
            // array_merge pisa los datos viejos con los nuevos
            $trabajos[$posicion] = array_merge($t, $_SESSION["cambios"][$t["id"]]);
        }
    }
}

// $clientes: la clave es el ID del cliente y el valor es su nombre.
// El ID lo asigna el sistema, nadie lo escribe a mano.
$clientes = [
    1 => "Marta Gómez",
    2 => "Pedro Díaz",
    3 => "Lucía Fernández",
    4 => "Carlos Ruiz",
    5 => "Ana Sosa",
];

$total_clientes = count($clientes);   // count cuenta cuántos elementos tiene la lista
