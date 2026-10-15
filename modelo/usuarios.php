<?php

class Usuario {
    public $Id;
    public $Nombre;
    public $Oficio;
    public $Telefono;
    public $Email;
    public $Imagen;

    public function __construct($Id = null, $Nombre = '', $Oficio = '', $Telefono = '', $Email = '', $Imagen = '') {
        $this->Id = $Id;
        $this->Nombre = $Nombre;
        $this->Oficio = $Oficio;
        $this->Telefono = $Telefono;
        $this->Email = $Email;
        $this->Imagen = $Imagen;
    }
    
    public static function obtenerTodas() {
        return [];
    }

    public static function obtenerPorId($Id) {
        $usuarios = self::obtenerTodas();
        foreach ($usuarios as $u) {
            if ($u->Id == $Id) { //revisar posible error de Id//
                return $u;
            }
        }
        return null;
    }

    public static function agregar($Nombre, $Oficio, $Telefono, $Email, $Imagen) {
        return true;
    }

    public static function editar($Id, $Nombre, $Oficio, $Telefono, $Email, $Imagen) {
        return true;
    }

    public static function eliminar($Id) {
        return true;
    }
}

