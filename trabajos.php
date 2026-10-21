<?php
// trabajos.php
// Lista de todos los trabajos, con pestañas por estado, buscador por cliente,
// resumen, total por cliente, estado de cobro y aviso de trabajos atrasados.
// Cada columna de la tabla se puede ordenar de menor a mayor y de mayor a menor
// tocando su encabezado (el orden viaja por la URL: ?orden=id&dir=desc).
// Usa datos de ejemplo (datos_ejemplo.php). No necesita JavaScript.

require "datos_ejemplo.php";

// Botón "Restablecer": borra los cambios hechos y vuelve a los datos de ejemplo
if (isset($_GET["restablecer"])) {
    unset($_SESSION["cambios"]);
    header("Location: trabajos.php");
    exit;
}

// Mensaje de aviso (lo dejan modificar_trabajo.php y cambiar_cobro.php). Se muestra una sola vez.
$mensaje = "";
if (isset($_SESSION["mensaje"])) {
    $mensaje = $_SESSION["mensaje"];
    unset($_SESSION["mensaje"]);
}

// ---------- Funciones auxiliares ----------

// Total de un trabajo = horas × precio por hora + materiales
function total_trabajo($t) {
    return $t["horas"] * $t["precio_hora"] + $t["materiales"];
}

// Muestra plata con formato: 32500 -> $ 32.500,00
function plata($numero) {
    return "$ " . number_format($numero, 2, ",", ".");
}

// Cuántos días pasaron desde una fecha (86400 = segundos que tiene un día)
function dias_desde($fecha) {
    return floor((time() - strtotime($fecha)) / 86400);
}

// Devuelve el dato de un trabajo que se usa para comparar, según la columna elegida.
// Los textos se pasan a minúscula con strtolower para que "ana" y "Ana" se ordenen igual.
function valor_columna($t, $columna) {
    if ($columna == "id") {
        return $t["id"];
    }
    if ($columna == "fecha") {
        return $t["fecha"];                       // AAAA-MM-DD se puede comparar como texto
    }
    if ($columna == "cliente") {
        return strtolower($t["cliente"]);
    }
    if ($columna == "trabajo") {
        return strtolower($t["descripcion"]);
    }
    if ($columna == "horas") {
        return $t["horas"];
    }
    if ($columna == "total") {
        return total_trabajo($t);
    }
    if ($columna == "estado") {
        return $t["estado"];                      // 0 = en curso, 1 = terminado
    }
    return $t["cobrado"];                         // la última columna posible es "cobro"
}

// ---------- 1) Leer los filtros de la URL ----------
// Los enlaces y el buscador mandan los datos por GET: trabajos.php?estado=curso&buscar=marta
$estado = "todos";
if (isset($_GET["estado"]) && in_array($_GET["estado"], ["todos", "curso", "terminados"])) {
    $estado = $_GET["estado"];
}

$buscar = "";
if (isset($_GET["buscar"])) {
    $buscar = trim($_GET["buscar"]);
}

// Columnas que se pueden ordenar: clave => título que ve la persona.
// La clave es lo que viaja por la URL (?orden=id). "Acciones" no está porque son botones.
$columnas = [
    "id"      => "ID",
    "fecha"   => "Fecha",
    "cliente" => "Cliente",
    "trabajo" => "Trabajo",
    "horas"   => "Horas",
    "total"   => "Total",
    "estado"  => "Estado",
    "cobro"   => "Cobro",
];

// Por defecto: más nuevos primero (fecha de mayor a menor)
$orden = "fecha";
// isset($columnas[...]) comprueba que la clave recibida exista (así no se acepta cualquier cosa)
if (isset($_GET["orden"]) && isset($columnas[$_GET["orden"]])) {
    $orden = $_GET["orden"];
}

// Sentido: asc = de menor a mayor, desc = de mayor a menor
$dir = "desc";
if (isset($_GET["dir"]) && in_array($_GET["dir"], ["asc", "desc"])) {
    $dir = $_GET["dir"];
}

// ---------- 2) Contar para las pestañas (siempre sobre todos los trabajos) ----------
$cant_todos = 0;
$cant_curso = 0;
$cant_terminados = 0;

foreach ($trabajos as $t) {
    $cant_todos = $cant_todos + 1;
    if ($t["estado"] == 0) {
        $cant_curso = $cant_curso + 1;
    } else {
        $cant_terminados = $cant_terminados + 1;
    }
}

// ---------- 3) Armar la lista filtrada ----------
$lista = [];

foreach ($trabajos as $t) {
    $pasa_estado = true;
    if ($estado == "curso" && $t["estado"] != 0) {
        $pasa_estado = false;
    }
    if ($estado == "terminados" && $t["estado"] != 1) {
        $pasa_estado = false;
    }

    // stripos busca un texto dentro de otro sin distinguir mayúsculas; false = no lo encontró
    $pasa_busqueda = true;
    if ($buscar != "" && stripos($t["cliente"], $buscar) === false) {
        $pasa_busqueda = false;
    }

    if ($pasa_estado && $pasa_busqueda) {
        $lista[] = $t;
    }
}

// ---------- Ordenar la lista ----------
// usort ordena usando una función que compara de a dos trabajos ($a y $b).
// El operador <=> devuelve -1, 0 o 1 según el primer valor sea menor, igual o mayor que el segundo.
// "use ($orden, $dir)" le presta a la función las variables de afuera (si no, no las vería).
usort($lista, function ($a, $b) use ($orden, $dir) {
    // Se comparan los datos de la columna elegida
    $resultado = valor_columna($a, $orden) <=> valor_columna($b, $orden);

    // Si son iguales (por ejemplo, dos trabajos del mismo cliente), desempata por ID
    if ($resultado == 0) {
        $resultado = $a["id"] <=> $b["id"];
    }

    // Para "de mayor a menor" se da vuelta el resultado
    if ($dir == "desc") {
        $resultado = -$resultado;
    }
    return $resultado;
});

// ---------- 4) Calcular el resumen y el total por cliente (sobre la lista filtrada) ----------
$suma_horas = 0;
$suma_total = 0;
$por_cobrar = 0;
$por_cliente = [];   // arreglo asociativo: nombre del cliente => total acumulado

foreach ($lista as $t) {
    $total = total_trabajo($t);
    $suma_horas = $suma_horas + $t["horas"];
    $suma_total = $suma_total + $total;

    // Pendiente de pago = trabajo terminado que todavía no se cobró
    if ($t["estado"] == 1 && $t["cobrado"] == 0) {
        $por_cobrar = $por_cobrar + $total;
    }

    $nombre = $t["cliente"];
    if (!isset($por_cliente[$nombre])) {
        $por_cliente[$nombre] = 0;    // primera vez que aparece este cliente
    }
    $por_cliente[$nombre] = $por_cliente[$nombre] + $total;
}

// arsort ordena de mayor a menor por valor, conservando los nombres
arsort($por_cliente);

// Atrasado = en curso hace más de este número de días
$dias_limite = 15;
?>
<!doctype html>
<html lang="es">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Trabajos | ServiTrack</title>
    <link rel="icon" href="./img/logo3.0.jpeg" />
    <link rel="stylesheet" href="css/style.css" />
    <link rel="stylesheet" href="css/trabajos.css" />
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

    <main class="pagina-trabajos">
      <!-- Atajo siempre visible (el menú de arriba se esconde en pantallas angostas) -->
      <p class="atajos"><a href="panel.php">← Volver al panel</a> <a href="trabajos.php?restablecer=1">Restablecer datos de ejemplo</a></p>

      <?php if ($mensaje != "") { ?>
        <p class="aviso"><?php echo htmlspecialchars($mensaje); ?></p>
      <?php } ?>
      <h1>Trabajos</h1>

      <!-- Pestañas: son enlaces. Se les agrega la búsqueda para que no se pierda al cambiar de pestaña.
           urlencode prepara el texto para ir dentro de una URL. -->
      <nav class="pestanas">
        <a href="trabajos.php?estado=todos&buscar=<?php echo urlencode($buscar); ?>&orden=<?php echo $orden; ?>&dir=<?php echo $dir; ?>"
           class="<?php if ($estado == 'todos') { echo 'activa'; } ?>">Todos (<?php echo $cant_todos; ?>)</a>
        <a href="trabajos.php?estado=curso&buscar=<?php echo urlencode($buscar); ?>&orden=<?php echo $orden; ?>&dir=<?php echo $dir; ?>"
           class="<?php if ($estado == 'curso') { echo 'activa'; } ?>">En curso (<?php echo $cant_curso; ?>)</a>
        <a href="trabajos.php?estado=terminados&buscar=<?php echo urlencode($buscar); ?>&orden=<?php echo $orden; ?>&dir=<?php echo $dir; ?>"
           class="<?php if ($estado == 'terminados') { echo 'activa'; } ?>">Terminados (<?php echo $cant_terminados; ?>)</a>
      </nav>

      <!-- Buscador: formulario con método GET (los datos viajan en la URL).
           El campo oculto "estado" mantiene la pestaña elegida al buscar. -->
      <form action="trabajos.php" method="get" class="buscador">
        <input type="hidden" name="estado" value="<?php echo $estado; ?>" />
        <label for="buscar">Buscar cliente</label>
        <input type="text" name="buscar" id="buscar" value="<?php echo htmlspecialchars($buscar); ?>" />
        <!-- Campos ocultos: mantienen el orden elegido en los encabezados al buscar -->
        <input type="hidden" name="orden" value="<?php echo $orden; ?>" />
        <input type="hidden" name="dir" value="<?php echo $dir; ?>" />
        <button type="submit" class="boton">Aplicar</button>
        <a href="trabajos.php" class="boton boton-sec">Limpiar</a>
      </form>

      <!-- Resumen de lo que se está viendo -->
      <section class="resumen-lista">
        <div class="dato"><strong><?php echo count($lista); ?></strong> trabajos</div>
        <div class="dato"><strong><?php echo $suma_horas; ?></strong> horas</div>
        <div class="dato"><strong><?php echo plata($suma_total); ?></strong> en total</div>
        <div class="dato dato-cobro"><strong><?php echo plata($por_cobrar); ?></strong> pendiente de pago</div>
      </section>

      <?php if (count($lista) == 0) { ?>
        <p class="vacio">No hay trabajos que coincidan con la búsqueda.</p>
      <?php } else { ?>

        <!-- Tabla: dentro de un contenedor que se desplaza si la pantalla es angosta -->
        <div class="tabla-contenedor">
          <table class="tabla">
            <tr>
              <!-- Un encabezado por cada columna de $columnas. Cada uno es un enlace que ordena por esa columna. -->
              <?php foreach ($columnas as $clave => $titulo) { ?>
                <?php
                  // Sentido al tocar: si la columna ya está de menor a mayor, pasa a mayor a menor;
                  // en cualquier otro caso empieza de menor a mayor.
                  $nuevo_dir = "asc";
                  if ($orden == $clave && $dir == "asc") {
                      $nuevo_dir = "desc";
                  }

                  // Flecha solo en la columna activa: ▲ menor a mayor, ▼ mayor a menor
                  $flecha = "";
                  if ($orden == $clave && $dir == "asc") {
                      $flecha = " ▲";
                  }
                  if ($orden == $clave && $dir == "desc") {
                      $flecha = " ▼";
                  }
                ?>
                <th>
                  <!-- El enlace conserva la pestaña y la búsqueda (estado y buscar) -->
                  <a class="orden-link <?php if ($orden == $clave) { echo 'activa'; } ?>"
                     href="trabajos.php?estado=<?php echo $estado; ?>&buscar=<?php echo urlencode($buscar); ?>&orden=<?php echo $clave; ?>&dir=<?php echo $nuevo_dir; ?>"><?php echo $titulo . $flecha; ?></a>
                </th>
              <?php } ?>
              <th>Acciones</th>
            </tr>
            <?php foreach ($lista as $t) { ?>
              <?php
                // Un trabajo en curso con más días que el límite se marca como atrasado
                $dias = dias_desde($t["fecha"]);
                $atrasado = ($t["estado"] == 0 && $dias > $dias_limite);
              ?>
              <tr class="<?php if ($atrasado) { echo 'fila-atrasada'; } ?>">
                <td>#<?php echo $t["id"]; ?></td>
                <td><?php echo date("d/m/Y", strtotime($t["fecha"])); ?></td>
                <td><?php echo htmlspecialchars($t["cliente"]); ?></td>
                <td><?php echo htmlspecialchars($t["descripcion"]); ?></td>
                <td><?php echo $t["horas"]; ?></td>
                <td><?php echo plata(total_trabajo($t)); ?></td>
                <td>
                  <?php if ($t["estado"] == 1) { ?>
                    <span class="etiqueta etiqueta-verde">Terminado</span>
                  <?php } else if ($atrasado) { ?>
                    <span class="etiqueta etiqueta-roja">Atrasado (<?php echo $dias; ?> días)</span>
                  <?php } else { ?>
                    <span class="etiqueta etiqueta-violeta">En curso</span>
                  <?php } ?>
                </td>
                <td>
                  <?php if ($t["cobrado"] == 1) { ?>
                    <span class="etiqueta etiqueta-verde">Cobrado</span>
                  <?php } else if ($t["estado"] == 1) { ?>
                    <span class="etiqueta etiqueta-naranja">Pendiente de pago</span>
                  <?php } else { ?>
                    -
                  <?php } ?>
                </td>
                <td class="acciones-celda">
                  <!-- Modificar: lleva a la página de edición de este trabajo (usa el ID en la URL) -->
                  <a class="btn-mini" href="modificar_trabajo.php?id=<?php echo $t["id"]; ?>">Modificar</a>

                  <!-- Cobro: un formulario chico que envía el ID por POST a cambiar_cobro.php.
                       "volver" guarda los filtros actuales para regresar a la misma vista. -->
                  <form action="cambiar_cobro.php" method="post">
                    <input type="hidden" name="id" value="<?php echo $t["id"]; ?>" />
                    <input type="hidden" name="volver" value="<?php echo htmlspecialchars($_SERVER["QUERY_STRING"]); ?>" />
                    <button type="submit" class="btn-mini">
                      <?php if ($t["cobrado"] == 1) { echo "Marcar pendiente de pago"; } else { echo "Marcar cobrado"; } ?>
                    </button>
                  </form>
                </td>
              </tr>
            <?php } ?>
          </table>
        </div>

        <!-- Total por cliente: cuánto suma cada cliente en los trabajos que se están viendo -->
        <section class="por-cliente">
          <h2>Total por cliente</h2>
          <ul>
            <?php foreach ($por_cliente as $nombre => $monto) { ?>
              <li>
                <span><?php echo htmlspecialchars($nombre); ?></span>
                <b><?php echo plata($monto); ?></b>
              </li>
            <?php } ?>
          </ul>
        </section>
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
