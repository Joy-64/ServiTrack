<?php
require_once __DIR__ . '/../core/Database.php';

class Usuario
{
    private const NOMBRE_CLASE = 'Usuario';
    public $Id;
    public $Nombre;
    public $Apellido;
    public $Sexo;
    public $CUIL;
    public $NDoc;
    public $FNacimiento;

    public $CodPostal;
    public $Localidad;
    public $Oficio;
    public $Telefono;
    public $Email;
    public $Imagen;

    public function __construct($Id = null, $Nombre = '', $Apellido = '', $Sexo = '', $CUIL = '', $NDoc = '', $FNacimiento = '', $CodPostal = '', $Localidad = '', $Oficio = '', $Telefono = '', $Email = '', $Imagen = '')
    {
        $this->Id = $Id;
        $this->Nombre = $Nombre;
        $this->Apellido = $Apellido;
        $this->Sexo = $Sexo;
        $this->CUIL = $CUIL;
        $this->NDoc = $NDoc;
        $this->FNacimiento = $FNacimiento;
        $this->CodPostal = $CodPostal;
        $this->Localidad = $Localidad;
        $this->Oficio = $Oficio;
        $this->Telefono = $Telefono;
        $this->Email = $Email;
        $this->Imagen = $Imagen;
    }

    public static function obtenerTodas()
    {
        try {
            $db = Database::getInstance()->getConnection();
            $stmt = $db->prepare('SELECT * FROM usuarios ORDER BY idUsuario DESC');
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_CLASS, self::NOMBRE_CLASE);
        } catch (PDOException $e) {
            error_log("Error al obtener usuarios: " . $e->getMessage());
            return [];
        };
    }

    public static function obtenerPorId($Id)
    {
        $usuarios = self::obtenerTodas();
        foreach ($usuarios as $u) {
            if ($u->Id == $Id) { 
                return $u;
            }
        }
        return null;
    }

    public static function agregar($datos)
    {
        try {
            $db = Database::getInstance()->getConnection();
            $db->beginTransaction();
            $stmt = $db->prepare('INSERT INTO trabajadores (nombre, apellido, sexo, cuil, ndoc, fNacimiento, imagen, idTdoc, IdUsuarios) 
            VALUES (:nombre, :apellido, :sexo, :cuil, :ndoc, :fNacimiento, :imagen, :idTdoc, :IdUsuarios)');
            $stmt->execute([
                ':nombre' => $datos['Nombre'],
                ':apellido' => $datos['Apellido'],
                ':sexo' => $datos['Sexo'],
                ':cuil' => $datos['CUIL'],
                ':ndoc' => $datos['NDoc'],
                ':fNacimiento' => $datos['FNacimiento'],
                ':imagen' => $datos['Imagen']?? null,
                ':idTdoc' => $datos['IdTdoc'] ?? null,
                ':IdUsuarios' => $datos['IdUsuarios'] ?? null
            ]);
            $idTrabajador = $db->lastInsertId(); self::agregarOficios($db, $idTrabajador, $datos['Oficios'] ?? []);
            
            self::guardarContacto($db, $idTrabajador, 'Teléfono', $datos['Telefono'] ?? '');
            self::guardarContacto($db, $idTrabajador, 'Email', $datos['Email'] ?? ''); 
            self::guardarDomicilio($db, $idTrabajador, $datos['Domicilio'] ?? '', $datos['CodPostal'] ?? '', $datos['Localidad'] ?? '');
            $db->commit();
            return $idTrabajador; 
        } catch (PDOException $e) {
            $db->rollBack();
            error_log("Error al agregar trabajador: " . $e->getMessage());
            return false;
        };
    }
    private static function agregarOficios($db, $idTrabajador, $oficios)
    {
         {
            
            $stmt = $db->prepare('INSERT INTO trabajador_oficio (id_trabajador, id_oficio) VALUES (:id_trabajador, :id_oficio)');
            foreach ($oficios as $oficio) {
                $oficio = trim($oficio);
                if ($oficio === '') {
                    continue; // Saltar oficios vacíos
                }
                // Obtener el ID del oficio a partir de su descripción
                $stmtOficio = $db->prepare('SELECT id FROM oficios WHERE descripcion = :descripcion LIMIT 1');
                $stmtOficio->execute([':descripcion' => $oficio]);
                $idOficio = $stmtOficio->fetchColumn();
                if (!$idOficio) {
                    $stmtInsertOficio = $db->prepare('INSERT INTO oficios (descripcion) VALUES (:descripcion)');
                    $stmtInsertOficio->execute([':descripcion' => $oficio]);
                    $idOficio = $db->lastInsertId();
                }
                if ($idOficio) {
                    $stmt->execute([
                        ':id_trabajador' => $idTrabajador,
                        ':id_oficio' => $idOficio
                    ]);
                }
            }
        } 
    }
    public static function editar($Id, $Nombre, $Apellido, $Sexo, $CUIL, $NDoc, $FNacimiento, $CodPostal, $Localidad, $Oficio, $Telefono, $Email, $Imagen)
    {
        return true;
    }

    public static function eliminar($Id)
    {
        try {
            $db = Database::getInstance()->getConnection();
            $db->beginTransaction();
            self::borrarRelaciones($db, $Id);
            $stmt = $db->prepare('DELETE FROM trabajadores WHERE idTrabajador = :id');
            $stmt->execute([':id' => $Id]);
            $db->commit();
            return true;
        } catch (PDOException $e) {
            $db->rollBack();
            error_log("Error al eliminar trabajador: " . $e->getMessage());
            return false;
        }
    }
    public static function obtenerOficios()
    {
        try {
            $db = Database::getInstance()->getConnection();
            $stmt = $db->prepare('SELECT descripcion FROM oficios ORDER BY descripcion');
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_COLUMN);
        } catch (PDOException $e) {
            error_log("Error al obtener oficios: " . $e->getMessage());
            return [];
        }
    }
    public static function obtenerOficiosDeUsuario($idTrabajador)
    {
        try {
            $db = Database::getInstance()->getConnection();
            $stmt = $db->prepare('SELECT o.descripcion FROM trabajador_oficio t INNER JOIN oficios o ON o.id = t.id_oficio WHERE t.id_trabajador = :id ORDER BY o.descripcion');
            $stmt->execute([':id' => $idTrabajador]);
            return $stmt->fetchAll(PDO::FETCH_COLUMN);
        } catch (PDOException $e) {
            error_log("Error al obtener oficios del trabajador: " . $e->getMessage());
            return [];
        }
    }
    private static function guardarContacto($db, $idTrabajador, $medio, $valor)
    {
        $valor = trim($valor);
        if ($valor === '') {
            return; // No guardar contactos vacíos
        }
        $stmt = $db->prepare('SELECT id FROM medios_contactos WHERE descripcion = :descripcion LIMIT 1');
        $stmt->execute([':descripcion' => $medio]);
        $idMedio = $stmt->fetchColumn();
        if (!$idMedio) {
            $stmt = $db->prepare('INSERT INTO medios_contactos (descripcion) VALUES (:descripcion)');
            $stmt->execute([':descripcion' => $medio]);
            $idMedio = $db->lastInsertId();
        }
        $stmt = $db->prepare('INSERT INTO trabajador_medios_contactos (id_trabajador, id_medio_contacto, valor) VALUES (:id_trabajador, :id_medio, :valor)');
        $stmt->execute([
            ':id_trabajador' => $idTrabajador,
            ':id_medio' => $idMedio,
            ':valor' => $valor
        ]);
        
    }
    private static function guardarDomicilio ($db, $idTrabajador, $calle, $codPostal, $localidad)
    {
        $calle = trim($calle);
        $codPostal = trim($codPostal);
        $localidad = trim($localidad);
        if ($calle === '' && $codPostal === '' && $localidad === '') {
            return; // No guardar domicilios vacíos
        }
        $idCodPostal = null;
        if ($codPostal !== '')
            {
                $stmt = $db->prepare('SELECT id FROM codigo_postal WHERE codpostal = :codpostal and localidad = :localidad LIMIT 1');
                $stmt->execute([':codpostal' => $codPostal, ':localidad' => $localidad]);
                $idCodPostal = $stmt->fetchColumn();
                if (!$idCodPostal) {
                    $stmt = $db->prepare('INSERT INTO codigo_postal (codpostal, localidad) VALUES (:codpostal, :localidad)');
                    $stmt->execute([':codpostal' => $codPostal, ':localidad' => $localidad]);
                    $idCodPostal = $db->lastInsertId();
                }
            } 

        $stmt = $db->prepare('INSERT INTO domicilio_trabajador (id_trabajador, id_cod_postal) VALUES (:id_trabajador, :id_cod_postal)');
        $stmt->execute([
            ':id_trabajador' => $idTrabajador,
            ':id_cod_postal' => $idCodPostal,
           
        ]);
    }
    private static function borrarRelaciones($db, $idTrabajador)
    {
        $stmt = $db->prepare('DELETE FROM trabajador_oficio WHERE id_trabajador = :id_trabajador');
        $stmt->execute([':id_trabajador' => $idTrabajador]);

        $stmt = $db->prepare('DELETE FROM trabajador_medios_contactos WHERE id_trabajador = :id_trabajador');
        $stmt->execute([':id_trabajador' => $idTrabajador]);

        $stmt = $db->prepare('DELETE FROM domicilio_trabajador WHERE id_trabajador = :id_trabajador');
        $stmt->execute([':id_trabajador' => $idTrabajador]);
    }
}
