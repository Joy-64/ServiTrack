<?php
/*
 * PANEL DE USUARIO - Perfil del trabajador (se ve y se edita en la misma página).
 *
 * Arquitectura: esta vista usa el modelo, y el modelo habla con la base de datos.
 *
 * Lo que esta página espera del MODELO (modelo/usuarios.php, clase Usuario):
 *   Usuario::obtenerPorId($id)            -> objeto con: Id, Nombre, Apellido, IdTDoc, NDoc, Cuil,
 *                                            FNacimiento, Sexo, Imagen, Telefono, Email,
 *                                            Domicilio, CodPostal, Localidad
 *   Usuario::obtenerOficios()             -> lista con todos los oficios de la tabla oficios
 *   Usuario::obtenerOficiosDeUsuario($id) -> lista con los oficios que ya tiene ese trabajador
 *
 * Lo que esta página espera de ACCIONES (usuarios/acciones.php):
 *   Recibe el formulario por POST (accion, id, nombre, apellido, tipo_doc, ndoc, cuil,
 *   fnacimiento, sexo, oficios[] (lista), telefono, email, domicilio, codpostal, localidad y el
 *   archivo imagen), guarda los datos y vuelve acá con ?id=N&guardado=1 (salió bien)
 *   o ?id=N&error=1 (falló).
 */

// Carga la clase Usuario (el modelo), que se encarga de leer la base de datos.
require_once 'modelo/usuarios.php';

// Lee el id de la dirección (panel_usuarios.php?id=3); si no hay id, es un perfil nuevo.
$id = $_GET['id'] ?? null;

// Pide al modelo los datos de ese trabajador; si no hay id, queda vacío (null).
$usuario = $id ? Usuario::obtenerPorId($id) : null;

// Pide los oficios de la tabla oficios para mostrarlos en el selector; si el modelo aún no los ofrece, queda una lista vacía.
$oficios = method_exists('Usuario', 'obtenerOficios') ? Usuario::obtenerOficios() : [];

// Pide los oficios que ya tiene este trabajador (en un perfil nuevo no hay ninguno).
$oficiosUsuario = $usuario ? Usuario::obtenerOficiosDeUsuario($usuario->Id) : [];

// Opciones de las listas desplegables (los números coinciden con el id de la tabla tipos_documentos).
$tiposDocumento = [1 => 'DNI', 2 => 'LE', 3 => 'LC', 4 => 'PASAPORTE'];
$opcionesSexo = ['Masculino', 'Femenino'];

// Función de ayuda: escribe un texto de forma segura, para que no se ejecute código ajeno dentro de la página.
function limpiar($texto)
{
    return htmlspecialchars($texto ?? '', ENT_QUOTES, 'UTF-8');
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de usuario</title>
    <link rel="icon" href="./img/logo3.0.jpeg">
    <link rel="stylesheet" href="./css/style.css">
    <!--LINKS para que funcione el mapa-->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
        integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.css" />


</head>

<body>
    <!--ACA esta la cabecera-->
    <header>
        <div class="header-logo">
            <img src="./img/logo2.0.png" width="200px" height="100px" alt="Logo ServiTrack">
        </div>
        <button id="menu-btn"><img src="./img/menu.png" width="20px" height="20px" alt="menu"></button>
        <nav class="header-nav" id="menu">
            <ul>
                <li><a class="items-menu" href="">¿Quiénes somos?</a></li>
                <li><a class="items-menu" href="">Nuestro trabajo</a></li>
                <li><a class="items-menu" href="">Demo</a></li>
                <li><a class="items-menu" href="">Contacto</a></li>
            </ul>
        </nav>
        <div class="header-boton"><button class="boton"><a class="login" href="">Acceso</a></button></div>
    </header>
    <!--ACA TERMINA-->

    <!-- CONTENIDO: clase-section deja el espacio justo para que el header fijo no tape el perfil. -->
    <section class="clase-section perfil">

        <!-- AVISOS: confirman si los cambios se guardaron o si hubo un problema. -->
        <?php if (isset($_GET['guardado'])): ?>
            <p class="perfil-aviso perfil-aviso-exito">Cambios guardados</p>
        <?php endif; ?>
        <?php if (isset($_GET['error'])): ?>
            <p class="perfil-aviso perfil-aviso-error">No se pudieron guardar los cambios. Revisá que los datos obligatorios estén completos e intentá de nuevo.</p>
        <?php endif; ?>

        <!-- PRESENTACIÓN: resumen del perfil con foto, nombre, oficio y ubicación, como el encabezado de LinkedIn. -->
        <article class="perfil-presentacion">
            <!-- Banner de color que va detrás de la foto. -->
            <div class="perfil-banner"></div>
            <div class="perfil-datos">
                <!-- Foto de perfil circular; si todavía no hay foto, se muestra un círculo gris. -->
                <?php if (!empty($usuario->Imagen)): ?>
                    <img src="<?php echo limpiar($usuario->Imagen); ?>" alt="Foto de perfil" width="130" height="130" class="perfil-foto">
                <?php else: ?>
                    <div class="perfil-foto perfil-foto-vacia">Sin foto</div>
                <?php endif; ?>

                <!-- Nombre completo; en un perfil nuevo invita a completar los datos. -->
                <h1 class="perfil-nombre"><?php echo $usuario ? limpiar($usuario->Nombre . ' ' . $usuario->Apellido) : 'Completá tu perfil'; ?></h1>
                <!-- Oficios del trabajador, que funcionan como el titular del perfil. -->
                <p class="perfil-titular"><?php echo !empty($oficiosUsuario) ? limpiar(implode(', ', $oficiosUsuario)) : 'Agregá tus oficios en la sección Oficios'; ?></p>
                <p class="perfil-ubicacion"><?php echo !empty($usuario->Localidad) ? limpiar($usuario->Localidad) : 'Agregá tu ubicación en la sección Domicilio'; ?></p>
            </div>
        </article>

        <!-- FORMULARIO: edita todos los datos del perfil; enctype permite enviar la foto junto con los textos. -->
        <form method="POST" action="usuarios/acciones.php" enctype="multipart/form-data">
            <!-- Campos ocultos: le dicen a acciones.php si hay que agregar o editar, y a quién. -->
            <input type="hidden" name="accion" value="<?php echo $usuario ? 'editar' : 'agregar'; ?>">
            <?php if ($usuario): ?>
                <input type="hidden" name="id" value="<?php echo limpiar($usuario->Id); ?>">
            <?php endif; ?>

            <!-- DATOS PERSONALES: los obligatorios son los que la tabla trabajadores no acepta vacíos. -->
            <article class="perfil-tarjeta">
                <h2 class="perfil-titulo">Datos personales</h2>
                <div class="perfil-fila">
                    <div class="contactanos-div">
                        <label for="nombre">Nombre</label>
                        <input type="text" id="nombre" name="nombre" value="<?php echo limpiar($usuario->Nombre ?? ''); ?>" required>
                    </div>
                    <div class="contactanos-div">
                        <label for="apellido">Apellido</label>
                        <input type="text" id="apellido" name="apellido" value="<?php echo limpiar($usuario->Apellido ?? ''); ?>" required>
                    </div>
                    <!-- Lista de tipos de documento; el valor que se guarda es el número de la opción. -->
                    <div class="contactanos-div">
                        <label for="tipo_doc">Tipo de documento</label>
                        <select id="tipo_doc" name="tipo_doc" required>
                            <option value="">Elegí una opción</option>
                            <?php foreach ($tiposDocumento as $numero => $descripcion): ?>
                                <option value="<?php echo $numero; ?>" <?php echo (($usuario->IdTDoc ?? '') == $numero) ? ' selected' : ''; ?>><?php echo $descripcion; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="contactanos-div">
                        <label for="ndoc">Número de documento</label>
                        <input type="number" id="ndoc" name="ndoc" value="<?php echo limpiar($usuario->NDoc ?? ''); ?>" required>
                    </div>
                    <div class="contactanos-div">
                        <label for="cuil">CUIL (sin guiones)</label>
                        <input type="number" id="cuil" name="cuil" value="<?php echo limpiar($usuario->Cuil ?? ''); ?>" required>
                    </div>
                    <div class="contactanos-div">
                        <label for="fnacimiento">Fecha de nacimiento</label>
                        <input type="date" id="fnacimiento" name="fnacimiento" value="<?php echo limpiar($usuario->FNacimiento ?? ''); ?>" required>
                    </div>
                    <div class="contactanos-div">
                        <label for="sexo">Sexo</label>
                        <select id="sexo" name="sexo" required>
                            <option value="">Elegí una opción</option>
                            <?php foreach ($opcionesSexo as $opcion): ?>
                                <option value="<?php echo $opcion; ?>" <?php echo (($usuario->Sexo ?? '') === $opcion) ? ' selected' : ''; ?>><?php echo $opcion; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
            </article>

            <!-- OFICIOS: se elige uno de la lista o se escribe en "Otro" (debajo del selector); los elegidos aparecen al lado, en fila, y se pueden quitar. -->
            <article class="perfil-tarjeta">
                <h2 class="perfil-titulo">Oficios</h2>
                <div class="perfil-fila">
                    <!-- Columna izquierda: el selector y, debajo, el campo "Otro". -->
                    <div>
                        <!-- Selector con los oficios guardados en la tabla oficios; al elegir uno se agrega a la lista. -->
                        <div class="contactanos-div">
                            <label for="selector-oficio">Elegí un oficio</label>
                            <select id="selector-oficio">
                                <option value="">Elegí una opción</option>
                                <?php foreach ($oficios as $oficio): ?>
                                    <option value="<?php echo limpiar($oficio); ?>"><?php echo limpiar($oficio); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <!-- Campo "Otro": para un oficio que no está en la lista; se suma con el botón o con Enter. -->
                        <div class="contactanos-div">
                            <label for="otro-oficio">Otro</label>
                            <input type="text" id="otro-oficio" placeholder="Escribí otro oficio">
                            <button type="button" id="agregar-otro" class="perfil-boton">Agregar</button>
                        </div>
                    </div>
                    <!-- Columna derecha: oficios elegidos en fila, cada uno con su botón para quitarlo, y el aviso que explica qué falta. -->
                    <div class="contactanos-div perfil-oficios-elegidos">
                        <p>Oficios elegidos</p>
                        <ul id="oficios-elegidos" class="perfil-oficios"></ul>
                        <p id="aviso-oficios"></p>
                    </div>
                </div>
            </article>

            <!-- CONTACTO: teléfono y correo por los que se puede ubicar al trabajador. -->
            <article class="perfil-tarjeta">
                <h2 class="perfil-titulo">Información de contacto</h2>
                <div class="perfil-fila">
                    <div class="contactanos-div">
                        <label for="telefono">Teléfono</label>
                        <input type="tel" id="telefono" name="telefono" value="<?php echo limpiar($usuario->Telefono ?? ''); ?>">
                    </div>
                    <div class="contactanos-div">
                        <label for="email">Correo electrónico</label>
                        <input type="email" id="email" name="email" value="<?php echo limpiar($usuario->Email ?? ''); ?>">
                    </div>
                </div>
            </article>

            <!-- DOMICILIO: dirección y código postal; la localidad aparece luego en la presentación. -->
            <article class="perfil-tarjeta">
                <h2 class="perfil-titulo">Domicilio</h2>
                <div class="perfil-fila">
                    <div class="contactanos-div">
                        <label for="domicilio">Calle y número</label>
                        <input type="text" id="domicilio" name="domicilio" value="<?php echo limpiar($usuario->Domicilio ?? ''); ?>">
                    </div>
                    <div class="contactanos-div">
                        <label for="codpostal">Código postal</label>
                        <input type="text" id="codpostal" name="codpostal" value="<?php echo limpiar($usuario->CodPostal ?? ''); ?>">
                    </div>
                    <div class="contactanos-div">
                        <label for="localidad">Localidad</label>
                        <input type="text" id="localidad" name="localidad" value="<?php echo limpiar($usuario->Localidad ?? ''); ?>">
                    </div>
                </div>
            </article>

            <!-- FOTO DE PERFIL: zona para arrastrar la imagen o elegirla con un clic; el input invisible cubre toda la zona. -->
            <article class="perfil-tarjeta">
                <h2 class="perfil-titulo">Foto de perfil</h2>
                <div class="perfil-zona-foto">
                    <p id="texto-imagen">Arrastrá una imagen hasta acá o hacé clic para elegirla</p>
                    <img id="vista-previa" alt="Vista previa de la foto elegida" width="100" height="100" class="perfil-vista-previa">
                    <input type="file" id="imagen" name="imagen" accept="image/*" class="perfil-input-foto">
                </div>
            </article>

            <!-- BOTONES: Guardar cambios envía el formulario; Cancelar vuelve a cargar el perfil sin guardar. -->
            <div class="perfil-botones">
                <button type="submit" class="boton">Guardar cambios</button>
                <a class="boton" href="panel_usuarios.php<?php echo $usuario ? '?id=' . limpiar($usuario->Id) : ''; ?>">Cancelar</a>
            </div>
        </form>
    </section>

    <!--ACA comienza el footer, que incluye logo sin texto, links como en el header y boton de descarga de app-->

    <footer class="footer">
        <div class="footer-logo">
            <img src="./img/logo 2.1.png" width="100px" height="100px" alt="Logo ServiTrack">
        </div>

        <nav class="foot-enlace">
            <a href="index.html">Inicio</a>
            <a href="quienes-somos.html">Quienes somos</a>
            <a href="contactanos.html">Contactanos</a>
            <a href="terminos-y-condiciones.html">Términos y condiciones</a>
        </nav>

        <p class="derechos">&copy; 2026 | ServiTrack. Todos los derechos reservados.</p>

    </footer>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- SCRIPT DE LA FOTO: cuando se elige o arrastra una imagen, muestra su nombre y una vista previa. -->
    <script>
        const campoImagen = document.getElementById('imagen');
        campoImagen.addEventListener('change', function() {
            const archivo = campoImagen.files[0];
            if (!archivo) {
                return;
            }
            document.getElementById('texto-imagen').textContent = archivo.name;
            const vistaPrevia = document.getElementById('vista-previa');
            vistaPrevia.src = URL.createObjectURL(archivo);
            vistaPrevia.classList.add('activa');
        });
    </script>
    <!-- SCRIPT DE OFICIOS: arma la lista de oficios elegidos, evita repetidos y permite quitarlos antes de guardar. -->
    <script>
        const selectorOficio = document.getElementById('selector-oficio');
        const campoOtro = document.getElementById('otro-oficio');
        const listaOficios = document.getElementById('oficios-elegidos');
        const avisoOficios = document.getElementById('aviso-oficios');

        // Muestra un mensaje cuando la lista está vacía y lo borra cuando ya hay oficios.
        function actualizarAviso() {
            avisoOficios.textContent = listaOficios.children.length === 0 ? 'Todavía no elegiste ningún oficio.' : '';
        }

        // Agrega un oficio a la lista: pone la primera letra en mayúscula, evita repetidos y crea el campo oculto que viaja al guardar.
        function agregarOficio(texto) {
            texto = texto.trim();
            if (texto === '') {
                return;
            }
            texto = texto.charAt(0).toUpperCase() + texto.slice(1).toLowerCase();
            const elegidos = listaOficios.querySelectorAll('input[name="oficios[]"]');
            for (const campo of elegidos) {
                if (campo.value.toLowerCase() === texto.toLowerCase()) {
                    return;
                }
            }
            const item = document.createElement('li');
            item.className = 'perfil-oficio-item';
            const nombre = document.createElement('span');
            nombre.textContent = texto;
            const oculto = document.createElement('input');
            oculto.type = 'hidden';
            oculto.name = 'oficios[]';
            oculto.value = texto;
            const quitar = document.createElement('button');
            quitar.type = 'button';
            quitar.className = 'perfil-boton';
            quitar.textContent = 'Quitar';
            quitar.addEventListener('click', function() {
                item.remove();
                actualizarAviso();
            });
            item.append(nombre, oculto, quitar);
            listaOficios.appendChild(item);
            actualizarAviso();
        }

        // Al elegir un oficio del selector, se agrega a la lista y el selector vuelve a su opción inicial.
        selectorOficio.addEventListener('change', function() {
            agregarOficio(selectorOficio.value);
            selectorOficio.value = '';
        });

        // El botón Agregar suma lo escrito en el campo "Otro" y lo deja vacío para el siguiente.
        document.getElementById('agregar-otro').addEventListener('click', function() {
            agregarOficio(campoOtro.value);
            campoOtro.value = '';
            campoOtro.focus();
        });

        // Con Enter en el campo "Otro" también se agrega el oficio, en vez de enviar el formulario.
        campoOtro.addEventListener('keydown', function(evento) {
            if (evento.key === 'Enter') {
                evento.preventDefault();
                agregarOficio(campoOtro.value);
                campoOtro.value = '';
            }
        });

        // Si el trabajador ya tiene oficios guardados, se cargan en la lista al abrir la página.
        const oficiosGuardados = <?php echo json_encode($oficiosUsuario, JSON_HEX_TAG | JSON_HEX_AMP); ?>;
        oficiosGuardados.forEach(function(oficio) {
            agregarOficio(oficio);
        });
        actualizarAviso();

        // Antes de guardar, exige al menos un oficio en la lista.
        document.querySelector('form').addEventListener('submit', function(evento) {
            if (listaOficios.children.length === 0) {
                evento.preventDefault();
                avisoOficios.textContent = 'Agregá al menos un oficio para poder guardar.';
                selectorOficio.focus();
            }
        });
    </script>
</body>


</html>