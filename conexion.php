<?php
// conexion.php
// Conecta PHP con la base de datos MySQL "servitrack" y deja listo el objeto $db.
// Se usa desde otros archivos con:  require_once "conexion.php";
// También define e(), una función que protege el texto antes de mostrarlo en HTML.

// Datos de conexión: cambialos por los tuyos.
$servername = "localhost";
$username   = "root";      // en XAMPP suele ser root
$password   = "";          // en XAMPP suele estar vacía
$dbname     = "servitrack";

// Mientras desarrollás dejalo en true: muestra el error real en pantalla.
// Cuando publiques la app, ponelo en false.
$depurar = true;

if ($depurar) {
    ini_set("display_errors", 1);
    error_reporting(E_ALL);
}

// Hace que los errores de MySQL se lancen como excepciones (más fáciles de detectar)
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

// Intenta conectar; si falla, corta la página y muestra el motivo
try {
    $db = new mysqli($servername, $username, $password, $dbname);
    $db->set_charset("utf8mb4");
} catch (mysqli_sql_exception $e) {
    http_response_code(500);
    exit($depurar
        ? "Error de conexión: " . htmlspecialchars($e->getMessage())
        : "No se pudo conectar con la base de datos.");
}

// e("texto") evita que un dato con código HTML rompa o ataque la página
// Atajo para escapar texto antes de mostrarlo en HTML
if (!function_exists("e")) {
    function e($texto) {
        return htmlspecialchars((string)$texto, ENT_QUOTES, 'UTF-8');
    }
}
