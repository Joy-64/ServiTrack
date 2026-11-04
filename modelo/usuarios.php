<?php
require_once __DIR__ . '/../core/Database.php';

class Usuario
{
    public $Id;
    public $Nombre;
    public $Oficio;
    public $Telefono;
    public $Email;
    public $Imagen;

    public function __construct($Id = null, $Nombre = '', $Oficio = '', $Telefono = '', $Email = '', $Imagen = '')
    {
        $this->Id = $Id;
        $this->Nombre = $Nombre;
        $this->Oficio = $Oficio;
        $this->Telefono = $Telefono;
        $this->Email = $Email;
        $this->Imagen = $Imagen;
    }

    public static function obtenerTodas()
    {
        return [];
    }

    public static function obtenerPorId($Id)
    {
        $usuarios = self::obtenerTodas();
        foreach ($usuarios as $u) {
            if ($u->Id == $Id) { //revisar posible error de Id//
                return $u;
            }
        }
        return null;
    }

    public static function agregar($Nombre, $Oficio, $Telefono, $Email, $Imagen)
    {
        return true;
    }

    public static function editar($Id, $Nombre, $Oficio, $Telefono, $Email, $Imagen)
    {
        return true;
    }

    public static function eliminar($Id)
    {
        return true;
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
}
