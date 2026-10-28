<?php
/*
 * PANEL DE USUARIO - Perfil del trabajador (se ve y se edita en la misma página).
 *
 * Arquitectura: esta vista usa el modelo, y el modelo habla con la base de datos.
 *
 * Lo que esta página espera del MODELO (modelo/usuarios.php, clase Usuario):
 *   Usuario::obtenerPorId($id) -> objeto con: Id, Nombre, Apellido, IdTDoc, NDoc, Cuil,
 *                                 FNacimiento, Sexo, Imagen, Oficio, Telefono, Email,
 *                                 Domicilio, CodPostal, Localidad
 *   Usuario::obtenerOficios()  -> lista con los oficios ya cargados (opcional)
 *
 * Lo que esta página espera de ACCIONES (usuarios/acciones.php):
 *   Recibe el formulario por POST (accion, id, nombre, apellido, tipo_doc, ndoc, cuil,
 *   fnacimiento, sexo, oficio, telefono, email, domicilio, codpostal, localidad y el
 *   archivo imagen), guarda los datos y vuelve acá con ?id=N&guardado=1 (salió bien)
 *   o ?id=N&error=1 (falló).
 */

// Carga la clase Usuario (el modelo), que se encarga de leer la base de datos.
require_once 'modelo/usuarios.php';

// Lee el id de la dirección (panel_usuarios.php?id=3); si no hay id, es un perfil nuevo.
$id = $_GET['id'] ?? null;

// Pide al modelo los datos de ese trabajador; si no hay id, queda vacío (null).
$usuario = $id ? Usuario::obtenerPorId($id) : null;

// Pide los oficios ya cargados para sugerirlos al escribir; si el modelo aún no los ofrece, queda una lista vacía.
$oficios = method_exists('Usuario', 'obtenerOficios') ? Usuario::obtenerOficios() : [];

// Opciones de las listas desplegables (los números coinciden con el id de la tabla tipos_documentos).
$tiposDocumento = [1 => 'DNI', 2 => 'LE', 3 => 'LC', 4 => 'PASAPORTE'];
$opcionesSexo = ['Masculino', 'Femenino', 'Otro'];

// Estilos que se repiten, armados con los colores y fuentes de style.css (así no hacen falta clases nuevas).
$estiloTarjeta = 'background-color: var(--blanco-suave); border-radius: 12px; padding: 1.5rem; margin-bottom: 1rem;';
$estiloTitulo = 'font-family: var(--subtitulo); color: var(--azul-oscuro); margin-bottom: 1rem;';
$estiloFila = 'display: flex; flex-wrap: wrap; gap: 0.5rem;';
$estiloAviso = 'padding: 10px 15px; margin-bottom: 1rem; border-radius: 12px;';

// Función de ayuda: escribe un texto de forma segura, para que no se ejecute código ajeno dentro de la página.
function limpiar($texto) {
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
    <section class="clase-section" style="font-family: var(--texto); color: var(--negro-suave);">

        <!-- AVISOS: confirman si los cambios se guardaron o si hubo un problema. -->
        <?php if (isset($_GET['guardado'])): ?>
        <p style="<?php echo $estiloAviso; ?> background-color: var(--verde-transparente);">Cambios guardados</p>
        <?php endif; ?>
        <?php if (isset($_GET['error'])): ?>
        <p style="<?php echo $estiloAviso; ?> background-color: var(--gris-oscuro); color: var(--blanco-suave);">No se pudieron guardar los cambios. Revisá que los datos obligatorios estén completos e intentá de nuevo.</p>
        <?php endif; ?>

        <!-- PRESENTACIÓN: resumen del perfil con foto, nombre, oficio y ubicación, como el encabezado de LinkedIn. -->
        <article style="background-color: var(--blanco-suave); border-radius: 12px; margin-bottom: 1rem; overflow: hidden;">
            <!-- Banner de color que va detrás de la foto. -->
            <div style="background-color: var(--azul-oscuro); height: 120px;"></div>
            <div style="padding: 0 1.5rem 1.5rem;">
                <!-- Foto de perfil circular; si todavía no hay foto, se muestra un círculo gris. -->
                <?php if (!empty($usuario->Imagen)): ?>
                <img src="<?php echo limpiar($usuario->Imagen); ?>" alt="Foto de perfil" width="130" height="130"
                    style="display: block; border-radius: 50%; object-fit: cover; margin-top: -65px; border: 4px solid var(--blanco-suave);">
                <?php else: ?>
                <div style="display: flex; align-items: center; justify-content: center; width: 130px; height: 130px; border-radius: 50%; margin-top: -65px; border: 4px solid var(--blanco-suave); background-color: var(--gris-oscuro); color: var(--blanco-suave);">Sin foto</div>
                <?php endif; ?>

                <!-- Nombre completo; en un perfil nuevo invita a completar los datos. -->
                <h1 style="font-family: var(--titulos); margin-top: 0.5rem;"><?php echo $usuario ? limpiar($usuario->Nombre . ' ' . $usuario->Apellido) : 'Completá tu perfil'; ?></h1>
                <!-- Oficio del trabajador, que funciona como el titular del perfil. -->
                <p style="font-family: var(--subtitulo);"><?php echo !empty($usuario->Oficio) ? limpiar($usuario->Oficio) : 'Agregá tu oficio en la sección Oficio'; ?></p>
                <!-- Localidad donde trabaja. -->
                <p style="color: var(--gris-oscuro);"><?php echo !empty($usuario->Localidad) ? limpiar($usuario->Localidad) : 'Agregá tu ubicación en la sección Domicilio'; ?></p>
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
            <article style="<?php echo $estiloTarjeta; ?>">
                <h2 style="<?php echo $estiloTitulo; ?>">Datos personales</h2>
                <div style="<?php echo $estiloFila; ?>">
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
                            <option value="<?php echo $numero; ?>"<?php echo (($usuario->IdTDoc ?? '') == $numero) ? ' selected' : ''; ?>><?php echo $descripcion; ?></option>
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
                            <option value="<?php echo $opcion; ?>"<?php echo (($usuario->Sexo ?? '') === $opcion) ? ' selected' : ''; ?>><?php echo $opcion; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
            </article>

            <!-- OFICIO: se escribe libremente y sugiere los oficios que ya existen para evitar duplicados. -->
            <article style="<?php echo $estiloTarjeta; ?>">
                <h2 style="<?php echo $estiloTitulo; ?>">Oficio</h2>
                <div class="contactanos-div">
                    <label for="oficio">Oficio que realizás</label>
                    <input type="text" id="oficio" name="oficio" list="lista-oficios" placeholder="Ej: Plomero" value="<?php echo limpiar($usuario->Oficio ?? ''); ?>" required>
                    <datalist id="lista-oficios">
                        <?php foreach ($oficios as $oficio): ?>
                        <option value="<?php echo limpiar($oficio); ?>">
                        <?php endforeach; ?>
                    </datalist>
                </div>
            </article>

            <!-- CONTACTO: teléfono y correo por los que se puede ubicar al trabajador. -->
            <article style="<?php echo $estiloTarjeta; ?>">
                <h2 style="<?php echo $estiloTitulo; ?>">Información de contacto</h2>
                <div style="<?php echo $estiloFila; ?>">
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
            <article style="<?php echo $estiloTarjeta; ?>">
                <h2 style="<?php echo $estiloTitulo; ?>">Domicilio</h2>
                <div style="<?php echo $estiloFila; ?>">
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
            <article style="<?php echo $estiloTarjeta; ?>">
                <h2 style="<?php echo $estiloTitulo; ?>">Foto de perfil</h2>
                <div style="position: relative; border: 2px dashed var(--azul-intermedio); border-radius: 12px; padding: 2rem; text-align: center; color: var(--gris-oscuro);">
                    <p id="texto-imagen">Arrastrá una imagen hasta acá o hacé clic para elegirla</p>
                    <img id="vista-previa" alt="Vista previa de la foto elegida" width="100" height="100"
                        style="display: none; margin: 1rem auto 0; border-radius: 50%; object-fit: cover;">
                    <input type="file" id="imagen" name="imagen" accept="image/*"
                        style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; opacity: 0; cursor: pointer;">
                </div>
            </article>

            <!-- BOTONES: Guardar cambios envía el formulario; Cancelar vuelve a cargar el perfil sin guardar. -->
            <div style="display: flex; gap: 1rem; margin-bottom: 1rem;">
                <button type="submit" class="boton">Guardar cambios</button>
                <a class="boton" style="color: var(--negro-suave);" href="panel_usuarios.php<?php echo $usuario ? '?id=' . limpiar($usuario->Id) : ''; ?>">Cancelar</a>
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
            <a href="servicios.html">Servicios</a>
            <a href="quienes-somos.html">Quienes somos</a>
            <a href="contacto.html">Contacto</a>
        </nav>

        <p class="derechos">&copy; @año | ServiTrack. Todos los derechos reservados.</p>

    </footer>
     <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- SCRIPT DE LA FOTO: cuando se elige o arrastra una imagen, muestra su nombre y una vista previa. -->
    <script>
        const campoImagen = document.getElementById('imagen');
        campoImagen.addEventListener('change', function () {
            const archivo = campoImagen.files[0];
            if (!archivo) {
                return;
            }
            document.getElementById('texto-imagen').textContent = archivo.name;
            const vistaPrevia = document.getElementById('vista-previa');
            vistaPrevia.src = URL.createObjectURL(archivo);
            vistaPrevia.style.display = 'block';
        });
    </script>
</body>


</html>
