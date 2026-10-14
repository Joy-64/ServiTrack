-- migracion_clientes.sql
-- Cambios de base de datos para el formulario CargaDeClientes.html.
-- Ejecutar UNA sola vez, en phpMyAdmin (pestaña SQL) con la base "servitrack" seleccionada.
-- Antes de ejecutar, hacé una copia de la base (Exportar).

-- 1) Campos nuevos en la tabla clientes
ALTER TABLE clientes
  ADD tipo ENUM('particular','empresa','consorcio') DEFAULT 'particular',
  ADD razon_social VARCHAR(100),
  ADD contacto VARCHAR(80),
  ADD whatsapp VARCHAR(20),
  ADD horario_contacto ENUM('manana','tarde','cualquiera') DEFAULT 'cualquiera',
  ADD origen VARCHAR(30),
  ADD documento VARCHAR(13),
  ADD condicion_iva VARCHAR(30),
  ADD observaciones TEXT,
  ADD activo TINYINT(1) DEFAULT 1,
  ADD fecha_alta DATE DEFAULT (CURDATE());

-- 2) Tabla de direcciones: un cliente puede tener varias
CREATE TABLE direcciones_cliente (
  id INT AUTO_INCREMENT PRIMARY KEY,
  id_cliente INT NOT NULL,
  etiqueta VARCHAR(30),               -- Casa, Local, Obra...
  calle_numero VARCHAR(100) NOT NULL,
  piso_depto VARCHAR(20),
  barrio VARCHAR(60),
  ciudad VARCHAR(60) NOT NULL,
  referencias VARCHAR(200),
  latitud DECIMAL(9,6),
  longitud DECIMAL(9,6),
  principal TINYINT(1) DEFAULT 0,
  FOREIGN KEY (id_cliente) REFERENCES clientes(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- 3) Pasar las direcciones que ya había en clientes.direccion a la tabla nueva
--    (la ciudad queda vacía porque antes no se guardaba)
INSERT INTO direcciones_cliente (id_cliente, etiqueta, calle_numero, ciudad, principal)
SELECT id, 'Casa', direccion, '', 1
FROM clientes
WHERE direccion IS NOT NULL AND direccion <> '';

-- La columna vieja clientes.direccion se deja por ahora, para no perder datos.
-- Cuando confirmes que todo funciona, se puede borrar:
-- ALTER TABLE clientes DROP COLUMN direccion;
