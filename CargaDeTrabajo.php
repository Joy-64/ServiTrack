<?php
// CargaDeTrabajo.php
// Recibe el formulario de CargaDeTrabajo.html (método POST), lo valida,
// calcula la mano de obra y el total, y muestra un resumen.
// TODAVÍA NO guarda en la base de datos: eso viene después.

// Si alguien entra escribiendo la dirección (sin enviar el formulario), lo mandamos al formulario
if ($_SERVER["REQUEST_METHOD"] != "POST") {
    header("Location: CargaDeTrabajo.html");
    exit;
}

// La lista de clientes (ID => nombre) viene de datos_ejemplo.php, igual que en las otras páginas
require "datos_ejemplo.php";

// Lista para mostrar el nombre del oficio en vez de su número (mismas opciones del formulario)
$oficios  = [1 => "Electricidad", 2 => "Plomería", 3 => "Albañilería", 4 => "Carpintería", 5 => "Pintura", 8 => "Gas"];

// 1) Leer los datos. $_POST["Nombre"] trae lo que se escribió en el campo con ese name.
$id_cliente = $_POST["IdCliente"];
$ubicacion  = trim($_POST["Ubicacion"]);          // trim saca espacios al principio y al final
$trabajo    = trim($_POST["TrabajoRealizado"]);
$estado     = $_POST["Estado"];
$desde      = $_POST["Desde"];
$hasta      = $_POST["Hasta"];
$presupuesto = trim($_POST["Presupuesto"]);

// (float) convierte el texto en número con decimales; un campo vacío da 0
$horas      = (float)$_POST["Horas"];
$precio     = (float)$_POST["PrecioHora"];
$materiales = (float)$_POST["Materiales"];

// Los tipos tildados llegan como lista. Si no se tildó ninguno, el dato no existe.
if (isset($_POST["TipoDeTrabajo"])) {
    $tipos = $_POST["TipoDeTrabajo"];
} else {
    $tipos = [];
}

// Comprobantes: contamos los archivos que realmente se eligieron (error 0 = subido bien)
$archivos = [];
foreach ($_FILES["Comprobante"]["name"] as $i => $nombre_archivo) {
    if ($_FILES["Comprobante"]["error"][$i] == 0) {
        $archivos[] = $nombre_archivo;
    }
}

// 2) Validar: cada problema se agrega a la lista $errores
$errores = [];

if (!isset($clientes[$id_cliente])) {
    $errores[] = "Elegí un cliente.";
}
if ($trabajo == "") {
    $errores[] = "Escribí el trabajo realizado.";
}
if (count($tipos) == 0) {
    $errores[] = "Elegí al menos un tipo de trabajo.";
}
if ($desde == "") {
    $errores[] = "Indicá la fecha de inicio.";
}
if ($hasta != "" && $hasta < $desde) {   // las fechas AAAA-MM-DD se pueden comparar como texto
    $errores[] = "La fecha \"Hasta\" no puede ser anterior a \"Desde\".";
}
if ($horas < 0 || $precio < 0 || $materiales < 0) {
    $errores[] = "Horas, precio y materiales no pueden ser negativos.";
}
if (count($archivos) > 4) {
    $errores[] = "Podés subir hasta 4 comprobantes.";
}

// 3) Calcular (el servidor siempre hace la cuenta, no se confía en el navegador)
$mano_de_obra = $horas * $precio;
$total        = $mano_de_obra + $materiales;

// ID automático: el trabajo nuevo recibe el número siguiente al más alto que existe.
// array_column saca el "id" de cada trabajo de la lista y max elige el mayor.
// (Con la base de datos esto lo hace MySQL solo, con AUTO_INCREMENT.)
$nuevo_id = max(array_column($trabajos, "id")) + 1;

// Función corta para mostrar plata: 32500 -> $ 32.500,00
function plata($numero) {
    return "$ " . number_format($numero, 2, ",", ".");
}
?>
<!doctype html>
<html lang="es">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Resumen del trabajo | ServiTrack</title>
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
        <!-- Caso 1: hay errores. Se muestran y no se calcula nada más -->
        <h1>Revisá el formulario</h1>
        <ul class="error-lista">
          <?php foreach ($errores as $error) { ?>
            <li><?php echo htmlspecialchars($error); ?></li>
          <?php } ?>
        </ul>
        <!-- Con el botón "atrás" del navegador (Alt + flecha izquierda) se conserva lo escrito -->
        <a class="boton" href="CargaDeTrabajo.html">Volver al formulario</a>

      <?php } else { ?>
        <!-- Caso 2: todo bien. Resumen con los cálculos -->
        <h1>Resumen del trabajo</h1>
        <p class="aviso">Todavía no se guarda en la base de datos. Revisá los datos y los cálculos.</p>

        <table class="resultado">
          <tr class="total"><th>ID del trabajo</th><td>#<?php echo $nuevo_id; ?> (lo genera el sistema)</td></tr>
          <tr><th>Cliente</th><td><?php echo htmlspecialchars($clientes[$id_cliente]); ?></td></tr>
          <tr><th>Ubicación</th><td><?php echo htmlspecialchars($ubicacion); ?></td></tr>
          <tr><th>Trabajo realizado</th><td><?php echo htmlspecialchars($trabajo); ?></td></tr>
          <tr>
            <th>Tipo de trabajo</th>
            <td>
              <?php
                // Convertimos cada id tildado en su nombre y los unimos con comas
                $nombres = [];
                foreach ($tipos as $id) {
                    if (isset($oficios[$id])) {
                        $nombres[] = $oficios[$id];
                    }
                }
                echo htmlspecialchars(implode(", ", $nombres));
              ?>
            </td>
          </tr>
          <tr><th>Estado</th><td><?php if ($estado == 1) { echo "Terminado"; } else { echo "En curso"; } ?></td></tr>
          <tr>
            <th>Fechas</th>
            <td>
              Desde <?php echo date("d/m/Y", strtotime($desde)); ?>
              <?php if ($hasta != "") { echo " hasta " . date("d/m/Y", strtotime($hasta)); } ?>
            </td>
          </tr>
          <tr><th>Detalle de materiales</th><td><?php echo htmlspecialchars($presupuesto); ?></td></tr>
          <tr>
            <th>Comprobantes</th>
            <td>
              <?php
                if (count($archivos) == 0) { echo "Ninguno"; }
                else { echo htmlspecialchars(implode(", ", $archivos)); }
              ?>
            </td>
          </tr>
        </table>

        <!-- Los cálculos -->
        <table class="resultado">
          <tr><th>Horas trabajadas</th><td><?php echo $horas; ?> hs</td></tr>
          <tr><th>Precio por hora</th><td><?php echo plata($precio); ?></td></tr>
          <tr><th>Mano de obra</th><td><?php echo $horas; ?> hs × <?php echo plata($precio); ?> = <?php echo plata($mano_de_obra); ?></td></tr>
          <tr><th>Materiales</th><td><?php echo plata($materiales); ?></td></tr>
          <tr class="total"><th>Total</th><td><?php echo plata($total); ?></td></tr>
        </table>

        <!-- Con el botón "atrás" del navegador (Alt + flecha izquierda) se conserva lo escrito -->
        <a class="boton" href="CargaDeTrabajo.html">Cargar otro trabajo</a>
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
