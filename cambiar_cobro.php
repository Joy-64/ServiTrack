<?php
// cambiar_cobro.php
// Recibe el botón "Marcar cobrado / Marcar pendiente de pago" de trabajos.php.
// Cambia el estado de cobro del trabajo y vuelve a la lista.
// Sin base de datos, el cambio se recuerda en la sesión (ver datos_ejemplo.php).

require "datos_ejemplo.php";   // trae $trabajos y arranca la sesión

// Si alguien entra escribiendo la dirección (sin apretar el botón), lo mandamos a la lista
if ($_SERVER["REQUEST_METHOD"] != "POST") {
    header("Location: trabajos.php");
    exit;
}

$id = (int)$_POST["id"];       // (int) convierte el texto en número entero

// "volver" son los filtros que tenía la lista (pestaña, búsqueda, orden)
$volver = "";
if (isset($_POST["volver"])) {
    $volver = $_POST["volver"];
}

// Buscamos el trabajo con ese ID y damos vuelta su estado de cobro
foreach ($trabajos as $t) {
    if ($t["id"] == $id) {
        $nuevo = 1 - $t["cobrado"];    // si era 1 pasa a 0, y si era 0 pasa a 1
        $_SESSION["cambios"][$id]["cobrado"] = $nuevo;

        if ($nuevo == 1) {
            $_SESSION["mensaje"] = "El trabajo #" . $id . " se marcó como cobrado.";
        } else {
            $_SESSION["mensaje"] = "El trabajo #" . $id . " quedó como pendiente de pago.";
        }
    }
}

header("Location: trabajos.php?" . $volver);
exit;
