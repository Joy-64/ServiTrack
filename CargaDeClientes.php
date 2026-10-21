<?php
// CargaDeClientes.php
// Recibe el formulario de CargaDeClientes.html (método POST), lo valida
// y muestra un resumen.
// TODAVÍA NO guarda en la base de datos: eso viene después.

// Lista de clientes existentes (ID => nombre), para calcular el próximo ID
require "datos_ejemplo.php";

// Si alguien entra sin enviar el formulario, lo mandamos al formulario
if ($_SERVER["REQUEST_METHOD"] != "POST") {
    header("Location: CargaDeClientes.html");
    exit;
}

// 1) Leer los datos (trim saca los espacios sobrantes)
$nombre        = trim($_POST["Nombre"]);
$tipo          = $_POST["Tipo"];
$razon_social  = trim($_POST["RazonSocial"]);
$telefono      = trim($_POST["Telefono"]);
$whatsapp      = trim($_POST["Whatsapp"]);
$mail          = trim($_POST["Mail"]);
$direccion     = trim($_POST["Direccion"]);
$ciudad        = trim($_POST["Ciudad"]);
$observaciones = trim($_POST["Observaciones"]);

// Una casilla sin marcar no se envía, por eso se pregunta si existe
if (isset($_POST["Activo"])) {
    $activo = "Sí";
} else {
    $activo = "No";
}

// Reemplaza la casilla "mismo número": si no se escribió WhatsApp, se usa el teléfono
if ($whatsapp == "") {
    $whatsapp = $telefono;
}

// 2) Validar
$errores = [];

if ($nombre == "") {
    $errores[] = "El nombre es obligatorio.";
}
// in_array pregunta si el valor está en la lista de opciones permitidas
if (!in_array($tipo, ["particular", "empresa", "consorcio"])) {
    $errores[] = "El tipo de cliente no es válido.";
}
// filter_var revisa que el mail tenga formato correcto (solo si se escribió uno)
if ($mail != "" && !filter_var($mail, FILTER_VALIDATE_EMAIL)) {
    $errores[] = "El mail no tiene un formato válido.";
}
// preg_match compara con un patrón: solo números, espacios, guiones, paréntesis y +
if ($telefono != "" && !preg_match("/^[0-9+\-() ]{6,20}$/", $telefono)) {
    $errores[] = "El teléfono solo puede tener números, espacios, guiones, paréntesis o +.";
}
if ($whatsapp != "" && !preg_match("/^[0-9+\-() ]{6,20}$/", $whatsapp)) {
    $errores[] = "El WhatsApp solo puede tener números, espacios, guiones, paréntesis o +.";
}
if ($direccion == "") {
    $errores[] = "Escribí la calle y el número.";
}
if ($ciudad == "") {
    $errores[] = "Escribí la ciudad.";
}
if ($tipo == "particular" && $razon_social != "") {
    $errores[] = "La razón social es solo para empresas o consorcios.";
}

// 3) ID automático: el cliente nuevo recibe el número siguiente al más alto que existe.
// array_keys saca los IDs de la lista y max elige el mayor. Si hay 1..5, el nuevo es 6.
// (Con la base de datos esto lo hace MySQL solo, con AUTO_INCREMENT.)
$nuevo_id = max(array_keys($clientes)) + 1;
?>
<!doctype html>
<html lang="es">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Resumen del cliente | ServiTrack</title>
    <link rel="icon" href="./img/logo3.0.jpeg" />
    <link rel="stylesheet" href="css/style.css" />
    <link rel="stylesheet" href="css/formularios.css" />
  </head>
  <body>
    <!-- Cabecera: logo y menú, igual en todas las páginas -->
    <header>
      <div class="header-logo">
        <a href="inicio.html">
          <img src="./img/logo2.0.png" width="200px" height="100px" alt="Logo ServiTrack" />
        </a>
      </div>
      <button id="menu-btn"><img src="./img/menu.png" width="20px" height="20px" alt="menu" /></button>
      <nav class="header-nav" id="menu">
        <ul>
          <li><a class="items-menu" href="panel.php">Panel</a></li>
          <li><a class="items-menu" href="CargaDeTrabajo.html">Cargar trabajo</a></li>
          <li><a class="items-menu" href="CargaDeClientes.html">Cargar cliente</a></li>
          <li><a class="items-menu" href="trabajos.php">Trabajos</a></li>
        </ul>
      </nav>
      <div class="header-boton"><button class="boton"><a class="login" href="">Acceso</a></button></div>
    </header>

    <main class="formulario">
      <!-- Atajos siempre visibles (el menú de arriba se esconde en pantallas angostas) -->
      <p class="atajos"><a href="panel.php">← Panel</a> <a href="trabajos.php">Ver todos los trabajos</a></p>

      <?php if (count($errores) > 0) { ?>
        <!-- Caso 1: hay errores -->
        <h1>Revisá el formulario</h1>
        <ul class="error-lista">
          <?php foreach ($errores as $error) { ?>
            <li><?php echo htmlspecialchars($error); ?></li>
          <?php } ?>
        </ul>
        <!-- Con el botón "atrás" del navegador (Alt + flecha izquierda) se conserva lo escrito -->
        <a class="boton" href="CargaDeClientes.html">Volver al formulario</a>

      <?php } else { ?>
        <!-- Caso 2: todo bien. Resumen del cliente -->
        <h1>Resumen del cliente</h1>
        <p class="aviso">Todavía no se guarda en la base de datos. Revisá los datos.</p>

        <table class="resultado">
          <tr class="total"><th>ID asignado</th><td>#<?php echo $nuevo_id; ?> (lo genera el sistema)</td></tr>
          <tr><th>Nombre</th><td><?php echo htmlspecialchars($nombre); ?></td></tr>
          <tr><th>Tipo</th><td><?php echo htmlspecialchars(ucfirst($tipo)); ?></td></tr>
          <?php if ($razon_social != "") { ?>
            <tr><th>Razón social</th><td><?php echo htmlspecialchars($razon_social); ?></td></tr>
          <?php } ?>
          <tr><th>Teléfono</th><td><?php echo htmlspecialchars($telefono); ?></td></tr>
          <tr><th>WhatsApp</th><td><?php echo htmlspecialchars($whatsapp); ?></td></tr>
          <tr><th>Mail</th><td><?php echo htmlspecialchars($mail); ?></td></tr>
          <tr><th>Dirección</th><td><?php echo htmlspecialchars($direccion . ", " . $ciudad); ?></td></tr>
          <tr><th>Observaciones</th><td><?php echo htmlspecialchars($observaciones); ?></td></tr>
          <tr><th>Activo</th><td><?php echo $activo; ?></td></tr>
        </table>

        <a class="boton" href="CargaDeClientes.html">Cargar otro cliente</a>
        <a class="boton boton-sec" href="panel.php">Ir al panel</a>
      <?php } ?>

    </main>

    <!-- Pie de página, igual en todas las páginas -->
    <footer class="footer">
      <div class="footer-logo">
        <a href="inicio.html">
          <img src="./img/logo 2.1.png" width="100px" height="100px" alt="Logo ServiTrack" />
        </a>
      </div>
      <nav class="foot-enlace">
        <a href="index.html">Inicio</a>
        <a href="servicios.html">Servicios</a>
        <a href="quienes-somos.html">Quienes somos</a>
        <a href="contacto.html">Contacto</a>
      </nav>
      <p class="derechos">&copy; <?php echo date("Y"); ?> | ServiTrack. Todos los derechos reservados.</p>
    </footer>
  </body>
</html>
