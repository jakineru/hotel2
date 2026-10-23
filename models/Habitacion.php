<?php
require_once __DIR__ . '/../config/Conexion.php';

class Habitacion {
    public static function obtenerTodas() {
        $db = Conexion::obtenerInstancia()->getPdo();
        $stmt = $db->query("SELECT * FROM habitaciones");
        return $stmt->fetchAll();
    }
}