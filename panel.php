<?php
// panel.php
// Pantalla principal de la app: resumen (pendientes, del mes, clientes),
// accesos rápidos a las acciones y listas de trabajos pendientes y últimos cargados.
// Funciona con datos de ejemplo o con la base de datos (ver $usarBD).
// Estilos: css/style.css + css/panel.css
// ================================================================
// MODO DE DATOS
//   false = usa los datos de ejemplo de abajo (no necesita base de datos)
//   true  = lee de MySQL (necesita conexion.php y la base importada)
// ================================================================
$usarBD = false;

if (!function_exists("e")) {
    function e($texto) {
        return htmlspecialchars((string)$texto, ENT_QUOTES, 'UTF-8');
    }
}
// Convierte 2026-10-14 en 14/10/2026 para mostrarlo
function fecha($f) { return $f ? date("d/m/Y", strtotime($f)) : "Sin fecha"; }

if ($usarBD) {
    require_once "conexion.php";

    // Números del resumen
    $pendientes = $db->query("SELECT COUNT(*) FROM trabajos WHERE estado = 0")->fetch_row()[0];
    $delMes     = $db->query("SELECT COUNT(*) FROM trabajos
                              WHERE YEAR(fecha) = YEAR(CURDATE()) AND MONTH(fecha) = MONTH(CURDATE())")->fetch_row()[0];
    $clientes   = $db->query("SELECT COUNT(*) FROM clientes")->fetch_row()[0];

    // Pendientes: los más viejos primero, porque son los que hay que atender antes
    $listaPendientes = $db->query(
        "SELECT t.id, t.fecha, t.descripcion, c.nombre
         FROM trabajos t
         LEFT JOIN clientes c ON c.id = t.id_cliente
         WHERE t.estado = 0
         ORDER BY t.fecha ASC
         LIMIT 5"
    )->fetch_all(MYSQLI_ASSOC);

    // Últimos 5 trabajos cargados
    $ultimos = $db->query(
        "SELECT t.id, t.fecha, t.horas, t.estado, c.nombre
         FROM trabajos t
         LEFT JOIN clientes c ON c.id = t.id_cliente
         ORDER BY t.fecha DESC, t.id DESC
         LIMIT 5"
    )->fetch_all(MYSQLI_ASSOC);
} else {
    // ----- DATOS DE EJEMPLO (misma forma que devuelve la base) -----
    // Para probar el diseño: editá, agregá o borrá filas de la lista de abajo.
    // estado: 0 = en curso, 1 = terminado
    $hoy = fn($dias) => date("Y-m-d", strtotime("-$dias days"));

    $trabajosEjemplo = [
        ["id" => 1, "nombre" => "Marta Gómez",   "fecha" => $hoy(14), "descripcion" => "Cambio de cañería en cocina y baño", "horas" => 6,   "estado" => 0],
        ["id" => 2, "nombre" => "Pedro Díaz",    "fecha" => $hoy(9),  "descripcion" => "Instalación eléctrica del living",   "horas" => 4.5, "estado" => 1],
        ["id" => 3, "nombre" => "Lucía Fernández","fecha" => $hoy(6), "descripcion" => "Pintura de dos habitaciones",         "horas" => 8,   "estado" => 0],
        ["id" => 4, "nombre" => "Carlos Ruiz",   "fecha" => $hoy(3),  "descripcion" => "Revisión de instalación de gas",     "horas" => 2,   "estado" => 1],
        ["id" => 5, "nombre" => "Marta Gómez",   "fecha" => $hoy(1),  "descripcion" => "Colocación de estantes a medida",    "horas" => 3.5, "estado" => 0],
        ["id" => 6, "nombre" => "Ana Sosa",      "fecha" => $hoy(0),  "descripcion" => "Arreglo de cerradura de entrada",    "horas" => 1,   "estado" => 1],
    ];
    $clientes = 5;

    $pendientes = count(array_filter($trabajosEjemplo, fn($t) => $t["estado"] == 0));
    $delMes     = count(array_filter($trabajosEjemplo, fn($t) => date("Ym", strtotime($t["fecha"])) === date("Ym")));

    $listaPendientes = array_values(array_filter($trabajosEjemplo, fn($t) => $t["estado"] == 0));
    usort($listaPendientes, fn($a, $b) => strcmp($a["fecha"], $b["fecha"]));

    $ultimos = $trabajosEjemplo;
    usort($ultimos, fn($a, $b) => strcmp($b["fecha"], $a["fecha"]) ?: $b["id"] <=> $a["id"]);
    $ultimos = array_slice($ultimos, 0, 5);
}
?>
<!doctype html>
<html lang="es">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Panel | ServiTrack</title>
    <link rel="icon" href="./img/logo3.0.jpeg" />
    <link rel="stylesheet" href="css/style.css" />
    <link rel="stylesheet" href="css/panel.css" />
  </head>
  <body>
    <!-- Cabecera con logo y menú (igual a las demás páginas) -->
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

    <!-- Contenido principal del panel -->
    <main class="panel">
      <h1 class="panel-titulo">Panel</h1>

      <!-- Resumen: tres contadores rápidos (el de pendientes va en violeta) -->
      <section class="resumen" aria-label="Resumen">
        <div class="dato dato-pend">
          <strong><?= (int)$pendientes ?></strong>
          <span>trabajos pendientes</span>
        </div>
        <div class="dato">
          <strong><?= (int)$delMes ?></strong>
          <span>trabajos este mes</span>
        </div>
        <div class="dato">
          <strong><?= (int)$clientes ?></strong>
          <span>clientes cargados</span>
        </div>
      </section>

      <!-- Accesos rápidos: tarjetas que llevan a las acciones principales -->
      <section aria-labelledby="t-acciones">
        <h2 id="t-acciones" class="panel-sub">¿Qué querés hacer?</h2>
        <div class="accesos">
          <a class="acceso" href="CargaDeTrabajo.html">
            <b>Cargar trabajo</b>
            <span>Registrá lo que hiciste, materiales y comprobantes.</span>
          </a>
          <a class="acceso" href="CargaDeClientes.html">
            <b>Cargar cliente</b>
            <span>Agregá los datos de un cliente nuevo.</span>
          </a>
          <a class="acceso" href="trabajos.php">
            <b>Ver todos los trabajos</b>
            <span>Buscá por cliente, estado o fecha.</span>
          </a>
        </div>
      </section>

      <!-- Listas: pendientes a la izquierda, últimos trabajos a la derecha.
           Si no hay datos, se muestra un mensaje con enlace para cargar uno -->
      <div class="listas">
        <section class="caja" aria-labelledby="t-pend">
          <h2 id="t-pend" class="panel-sub">Trabajos pendientes</h2>
          <?php if (!$listaPendientes): ?>
            <p class="vacio">No tenés trabajos pendientes. <a href="CargaDeTrabajo.html">Cargar uno</a></p>
          <?php else: ?>
            <ul class="items">
              <?php foreach ($listaPendientes as $t): ?>
                <li>
                  <div>
                    <b><?= e($t["nombre"] ?? "Cliente sin nombre") ?></b>
                    <span><?= e(mb_strimwidth($t["descripcion"] ?? "Sin descripción", 0, 70, "…")) ?></span>
                  </div>
                  <small>Desde <?= fecha($t["fecha"]) ?></small>
                </li>
              <?php endforeach; ?>
            </ul>
          <?php endif; ?>
        </section>

        <section class="caja" aria-labelledby="t-ult">
          <h2 id="t-ult" class="panel-sub">Últimos trabajos</h2>
          <?php if (!$ultimos): ?>
            <p class="vacio">Todavía no cargaste trabajos. <a href="CargaDeTrabajo.html">Cargar el primero</a></p>
          <?php else: ?>
            <ul class="items">
              <?php foreach ($ultimos as $t): ?>
                <li>
                  <div>
                    <b><?= e($t["nombre"] ?? "Cliente sin nombre") ?></b>
                    <span><?= fecha($t["fecha"]) ?> · <?= e(rtrim(rtrim(number_format((float)$t["horas"], 2, ",", ""), "0"), ",")) ?> hs</span>
                  </div>
                  <?php if ((int)$t["estado"] === 1): ?>
                    <small class="estado estado-ok">Terminado</small>
                  <?php else: ?>
                    <small class="estado estado-pend">En curso</small>
                  <?php endif; ?>
                </li>
              <?php endforeach; ?>
            </ul>
          <?php endif; ?>
        </section>
      </div>
    </main>

    <!-- Pie de página (igual a las demás páginas) -->
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
      <p class="derechos">&copy; <?= date("Y") ?> | ServiTrack. Todos los derechos reservados.</p>
    </footer>
  </body>
</html>
